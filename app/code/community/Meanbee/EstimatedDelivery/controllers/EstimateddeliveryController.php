<?php

class Meanbee_EstimatedDelivery_EstimateddeliveryController extends Mage_Adminhtml_Controller_Action {

    public function viewAction() {
        $this->_title('Estimated Delivery');

        $this->loadLayout();
        $this->renderLayout();

    }
}
