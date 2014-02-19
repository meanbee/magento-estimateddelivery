<?php

class Meanbee_EstimatedDelivery_Block_Adminhtml_Estimateddelivery_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct($attributes = array()) {
        parent::__construct($attributes);

        $this->setId('meanbee_estimateddelivery_grid');
        $this->setSaveParametersInSession(false);
    }

}
