<?php

class Meanbee_EstimatedDelivery_Block_Adminhtml_Estimateddelivery extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        parent::__construct();

        $this->_headerText = 'Estimated Delivery';

        $this->_controller = 'adminhtml_estimateddelivery';
        $this->_blockGroup = 'meanbee_estimateddelivery';
    }
}
