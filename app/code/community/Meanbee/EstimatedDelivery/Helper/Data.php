<?php

class Meanbee_EstimatedDelivery_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $_estimatedDeliveryData = array();
    protected $_dispatchDate = array();
    protected $_estimatedDeliveryFrom = array();
    protected $_estimatedDeliveryTo = array();


    public function getEstimatedDeliveryText($shippingMethod, $date = null) {
        $from = $this->getEstimatedDeliveryFromString($shippingMethod, $date);
        $to = $this->getEstimatedDeliveryToString($shippingMethod, $date);
        return sprintf('Estimated delivery: %s - %s.', $from, $to);
    }

    /**
     * Compute the estimated delivery upper bound. $date default to today's date if not provided.
     *
     * @param $shippingMethod
     * @param Zend_Date|null $date
     * @return Zend_Date
     */
    public function getEstimatedDeliveryTo($shippingMethod, $date = null) {
        $date = $this->_initDate($date);
        $cacheKey = $shippingMethod.$date->toString('ddMMyy');

        if (isset($this->_estimatedDeliveryTo[$cacheKey]) && $cached = $this->_estimatedDeliveryTo[$cacheKey]) {
            return $cached;
        }

        $deliveryFromDate = $this->getEstimatedDeliveryFrom($shippingMethod, $date);
        $estimatedDelivery = $this->_getEstimatedDeliveryData($shippingMethod);
        $offset = $estimatedDelivery->getEstimatedDeliveryTo() - $estimatedDelivery->getEstimatedDeliveryFrom();
        $deliveryToDate = $this->_computeEstimatedDelivery($shippingMethod, $deliveryFromDate, $offset);

        $this->_estimatedDeliveryTo[$cacheKey] = $deliveryToDate;

        return $deliveryToDate;
    }


    /**
     * Compute the estimated delivery lower bound. $date default to today's date if not provided.
     *
     * @param $shippingMethod
     * @param Zend_Date|null $date
     * @return Zend_Date
     */
    public function getEstimatedDeliveryFrom($shippingMethod, $date = null) {
        $date = $this->_initDate($date);
        $cacheKey = $shippingMethod.$date->toString('ddMMyy');

        if (isset($this->_estimatedDeliveryFrom[$cacheKey]) && $cached = $this->_estimatedDeliveryFrom[$cacheKey]) {
            return $cached;
        }

        $dispatchDate = $this->getDispatchDate($shippingMethod, $date);
        $estimatedDelivery = $this->_getEstimatedDeliveryData($shippingMethod);
        $deliveryFromDate = $this->_computeEstimatedDelivery($shippingMethod, $dispatchDate, $estimatedDelivery->getEstimatedDeliveryFrom());

        $this->_estimatedDeliveryFrom[$cacheKey] = $deliveryFromDate;

        return $deliveryFromDate;
    }

    /**
     * Compute the date of dispatch. $date defaults to today if not provided.
     *
     * @param $shippingMethod
     * @param Zend_Date|null $date
     * @return Zend_Date
     */
    public function getDispatchDate($shippingMethod, $date = null) {
        $date = $this->_initDate($date);
        $cacheKey = $shippingMethod.$date->toString('ddMMyy');

        if (isset($this->_dispatchDate[$cacheKey]) && $cached = $this->_dispatchDate[$cacheKey]) {
            return $cached;
        }

        $estimatedDelivery = $this->_getEstimatedDeliveryData($shippingMethod);

        $date = $this->_handleLatestDispatch($estimatedDelivery, $date);
        $date = $this->_handleDispatchPreparation($estimatedDelivery, $date);
        $date = $this->_computeClosestValidDate($estimatedDelivery->getDispatchableDays(), $date);

        $this->_dispatchDate[$cacheKey] = $date;

        return $date;
    }

    /**
     * Helper method around getEstimatedDeliveryFrom which returns the estimated delivery as a formatted date string,
     * rather than a Zend_Date.
     *
     * @param $shippingMethod
     * @param Zend_Date|null $date
     * @param string $format
     * @return string
     */
    public function getEstimatedDeliveryFromString($shippingMethod, $date = null, $format = 'EEEE, dSS MMMM') {
        $result = $this->getEstimatedDeliveryFrom($shippingMethod, $date);
        return $result->toString($format);
    }

    /**
     * Helper method around getEstimatedDeliveryTo which returns the estimated delivery as a formatted date string,
     * rather than a Zend_Date.
     *
     * @param $shippingMethod
     * @param Zend_Date|null $date
     * @param string $format
     * @return string
     */
    public function getEstimatedDeliveryToString($shippingMethod, $date = null, $format = 'EEEE, dSS MMMM') {
        $result = $this->getEstimatedDeliveryTo($shippingMethod, $date);
        return $result->toString($format);
    }

    /**
     * Helper method around getDispatchDate which returns the estimated delivery as a formatted date string,
     * rather than a Zend_Date.
     *
     * @param $shippingMethod
     * @param Zend_Date|null $date
     * @param string $format
     * @return string
     */
    public function getDispatchDateString($shippingMethod, $date = null, $format = 'EEEE, dSS MMMM') {
        $result = $this->getDispatchDate($shippingMethod, $date);
        return $result->toString($format);
    }

    protected function _initDate($date) {
        if ($date === null) {
            $date = Mage::app()->getLocale()->date();
        }
        return $date;
    }

    /**
     * Helper method, sets up the call to _computeClosestValidDate. $offset is the amount to increment the date by - see
     * how this is used in getEstimateDeliveryFrom and getEstimateDeliveryTo.
     *
     * @param array $shippingMethod
     * @param Zend_Date $date
     * @param int $offset
     * @return Zend_Date
     */
    protected function _computeEstimatedDelivery($shippingMethod, $date, $offset) {
        $localDate = clone $date;
        $estimatedDelivery = $this->_getEstimatedDeliveryData($shippingMethod);

        $localDate->addDay($offset);
        $localDate = $this->_computeClosestValidDate($estimatedDelivery->getDeliverableDays(), $localDate);

        return $localDate;
    }

    /**
     * Helper method which finds the closest valid day for a given date. Shipping methods have certain days which they
     * can be either delivered or dispatched and this helper method facilitates finding the closest day.
     *
     * For example, imagine "freeshipping" can be dispatched on Monday to Friday, and an order is placed on Sunday.
     * This method would return the date of the Monday following the Sunday which the order was placed.
     *
     * @param array $validDays
     * @param Zend_Date $date
     * @return Zend_Date
     */
    protected function _computeClosestValidDate($validDays, $date) {
        $localDate = clone $date;

        while(true) {
            $day = $localDate->toString(Zend_Date::WEEKDAY_DIGIT);
            if (in_array($day, $validDays)) {
                break;
            }
            $localDate->addDay(1);
        }

        return $localDate;
    }

    protected function _getEstimatedDeliveryData($shippingMethod) {
        if (isset($this->_estimatedDeliveryData[$shippingMethod]) && $data = $this->_estimatedDeliveryData[$shippingMethod]) {
            return $data;
        }
        $data = Mage::getModel('meanbee_estimateddelivery/estimateddelivery')->loadByShippingMethod($shippingMethod);
        $this->_estimatedDeliveryData[$shippingMethod] = $data;
        return $data;
    }

    /**
     * If we are past the latest dispatch point on a day, increment the day by one, since this means the order would
     * be dispatched on the following day.
     *
     * @param Meanbee_EstimatedDelivery_Model_Estimateddelivery $estimatedDelivery
     * @param Zend_Date $date
     * @return Zend_Date
     */
    protected function _handleLatestDispatch($estimatedDelivery, $date) {
        $localDate = clone $date;
        $latestDispatchTime = intval(str_replace(':', '', $estimatedDelivery->getLastDispatchTime()));
        $currentTime = intval($localDate->toString('HHmmss'));

        if ($currentTime >= $latestDispatchTime) {
            $localDate->addDay(1);
        }

        return $localDate;
    }

    /**
     * Increment the date by the dispatch prepartion amount configured in admin.
     *
     * @param Meanbee_EstimatedDelivery_Model_Estimateddelivery $estimatedDelivery
     * @param Zend_Date $date
     * @return Zend_Date
     */
    protected function _handleDispatchPreparation($estimatedDelivery, $date) {
        $localDate = clone $date;
        $dispatchPreparation = $estimatedDelivery->getDispatchPreparation();

        if ($dispatchPreparation) {
            $localDate->addDay($dispatchPreparation);
        }

        return $localDate;
    }
}