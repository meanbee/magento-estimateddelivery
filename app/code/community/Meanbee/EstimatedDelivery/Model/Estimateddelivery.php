<?php
class Meanbee_EstimatedDelivery_Model_Estimateddelivery extends Mage_Core_Model_Abstract {

    public function __construct() {
        $this->_init('meanbee_estimateddelivery/estimateddelivery');
    }

    public function validate() {
        $errors = array();

        if ($this->getEstimatedDeliveryFrom() > $this->getEstimatedDeliveryTo()) {
            $errors []= Mage::helper('meanbee_estimateddelivery')->__('Estimated Delivery Days (Lower Bound) cannot be larger than Estimated Delivery Days (Upper Bound)');
        }

        if (empty($errors)) {
            return true;
        }

        return $errors;
    }
}
