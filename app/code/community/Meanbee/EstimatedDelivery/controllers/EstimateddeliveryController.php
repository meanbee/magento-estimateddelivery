<?php

class Meanbee_EstimatedDelivery_EstimateddeliveryController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_title('Estimated Delivery');

        $this->_loadLayout();
        $this->renderLayout();
    }

    public function editAction() {
        $this->_title('Edit Estimated Delivery');

        $this->_loadLayout();
        $this->renderLayout();
    }

    protected function _loadLayout() {
        return $this->loadLayout()
            ->_setActiveMenu('sales/meanbee_estimateddelivery');
    }
}
