<?php

class Meanbee_EstimatedDelivery_Block_Checkout_Onepage_Success_Estimateddelivery extends Mage_Checkout_Block_Onepage_Success {

    public function getOrder() {
        $order = null;
        $incrementId = $this->getData('order_id');

        if ($incrementId) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
        }

        return $order;


    }
}