<?php

class Meanbee_EstimatedDelivery_Block_Adminhtml_Estimateddelivery_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_blockGroup = 'meanbee_estimateddelivery';
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_estimateddelivery';

        $this->_addButton('save_and_continue_edit', array(
            'class'   => 'save',
            'label'   => Mage::helper('meanbee_estimateddelivery')->__('Save and Continue Edit'),
            'onclick' => 'editForm.submit($(\'edit_form\').action + \'back/edit/\')',
        ), 10);

        $this->_updateButton('save', 'label', Mage::helper('rating')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('rating')->__('Delete'));

        if($id = $this->getRequest()->getParam($this->_objectId)) {

            $data = Mage::getModel('meanbee_estimateddelivery/estimateddelivery')
                ->load($id);
            $data->addShippingMethods();


            // Check to see if the methods already exist in the db, and throw warning if so
            $data->handleExistingShippingMethods();

            Mage::register('estimatedelivery_data', $data);
        }
    }

    public function getHeaderText() {
        $data = Mage::registry('estimatedelivery_data');
        if($data && $data->getId()) {
            return Mage::helper('meanbee_estimateddelivery')->__("Edit Estimate Delivery");
        } else {
            return Mage::helper('meanbee_estimateddelivery')->__('New Estimate Delivery');
        }
    }
}
