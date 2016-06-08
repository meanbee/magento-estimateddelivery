<?php
class Meanbee_EstimatedDelivery_Model_Observer
{
    /**
     * Save chosen slot to quote object.
     *
     * @param  Varien_Event_Observer $observer
     */
    public function saveDataToQuote($observer)
    {
        $quote = $observer->getQuote() ?: Mage::getSingleton('checkout/session')->getQuote();
        $this->saveDeliverySlot($quote);
        $this->saveDispatchDate($quote);
        if ($observer->getEvent()->getName() !== 'sales_quote_save_before') {
            $quote->save();
        }
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     */
    private function saveDeliverySlot($quote)
    {
        $fields = Mage::app()->getFrontController()->getRequest()->getPost();
        $year = isset($fields['slot-year']) ? $fields['slot-year'] : null;
        $month = isset($fields['slot-month']) ? str_pad($fields['slot-month'] + 1, 2, '0', STR_PAD_LEFT) : null;
        $week = isset($fields['slot-week']) ? $fields['slot-week'] + 1  : null;
        $day = isset($fields['slot-day']) ? str_pad($fields['slot-day'] + 1, 2, '0', STR_PAD_LEFT) : null;
        $shippingMethod = isset($fields['shipping_method']) ? $fields['shipping_method'] : $quote->getShippingAddress()->getShippingMethod();
        $resolution = Mage::helper('meanbee_estimateddelivery')->getSelectSlotResolution($shippingMethod);
        $deliverySlot = $quote->getDeliverySlot();
        if ($year && $month && $day) {
            $deliverySlot = "{$year}-{$month}-{$day}";
        } else if ($year && $month && $week) {
            // NOT IMPLEMENTED
        } else if ($year && $month) {
            $deliverySlot = "{$year}-{$month}";
        } else if (!$deliverySlot && isset($fields['shipping_method'])) {
            $earliestDelivery = Mage::helper('meanbee_estimateddelivery')->getEstimatedDeliveryFrom($fields['shipping_method']);
            switch ($resolution) {
                case Meanbee_EstimatedDelivery_Model_Source_TimeResolution::DAY:
                    $deliverySlot = $earliestDelivery->get('YYYY-MM-dd');
                    break;
                case Meanbee_EstimatedDelivery_Model_Source_TimeResolution::WEEK:
                    // NOT IMPLEMENTED
                    break;
                case Meanbee_EstimatedDelivery_Model_Source_TimeResolution::MONTH:
                    $deliverySlot = $earliestDelivery->get('YYYY-MM');
                    break;
            }
        }
        $quote->setDeliverySlot($resolution ? $deliverySlot : null);
    }

    private function saveDispatchDate($quote) {
        /** @var Meanbee_EstimatedDelivery_Helper_Data $helper */
        $helper = Mage::helper('meanbee_estimateddelivery');

        $shipping_method = $quote->getShippingAddress()->getShippingMethod();
        $dispatch_date = $helper->getDispatchDate($shipping_method);
        if (!$dispatch_date) {
            return;
        }
        $matches = array();
        switch ($helper->getSelectSlotResolution($shipping_method)) {
            case Meanbee_EstimatedDelivery_Model_Source_TimeResolution::DAY:
            case Meanbee_EstimatedDelivery_Model_Source_TimeResolution::MONTH:
                preg_match('/^(\d{4,})-(\d{2})(?:-(\d{2}))?$/', $quote->getDelierySlot());
                $delivery_date = new Zend_Date($quote->getDeliverySlot(), 'YYYY-MM-dd');
                $delayed_dispatch = $helper->getDelayedDispatch($shipping_method, $delivery_date);
                $actual_dispatch = $delayed_dispatch->compare($dispatch_date) === 1 ? $delayed_dispatch : $dispatch_date;
                $quote->setDispatchDate($actual_dispatch->get('YYYY-MM-dd'));
                break;
            case Meanbee_EstimatedDelivery_Model_Source_TimeResolution::WEEK:
                // NOT IMPLEMENTED
                break;
            default:
                $quote->setDispatchDate($dispatch_date->get('YYYY-MM-dd'));
                break;
        }
    }
}
