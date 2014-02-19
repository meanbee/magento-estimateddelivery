<?php

class Meanbee_EstimatedDelivery_EstimateddeliveryController extends Mage_Adminhtml_Controller_Action {

    public function viewAction() {
        $this->_title('Estimated Delivery');

        $this->loadLayout();
        $this->renderLayout();
    }

    public function editAction() {
        $this->_title('Edit Estimated Delivery');

        $this->loadLayout()
            ->_setActiveMenu('sales/meanbee_estimateddelivery')
        ;

        $this->renderLayout();
    }
}
