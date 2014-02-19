<?php
class Meanbee_EstimatedDelivery_Block_Adminhtml_Estimateddelivery_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('meanbee_estimateddelivery/estimateddelivery')->load($id);

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $id)),
            'method' => 'post',
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('meanbee_estimateddelivery')->__('Estimated Delivery Configuration'),
            'class'     => 'fieldset-wide',
        ));

        $fieldset->addField('shipping_method', 'text', array(
            'label'    => 'Shipping Method',
            'title'    => 'Shipping Method',
            'name'     => 'shipping_method',
            'required'  => true
        ));

        $fieldset->addField('dispatch_preparation', 'text', array(
            'label'    => 'Dispatch Preparation Time (Days)',
            'title'    => 'Dispatch Preparation Time (Days)',
            'name'     => 'dispatch_preparation',
            'required'  => true
        ));

        $fieldset->addField('dispatchable_days', 'multiselect', array(
            'label'    => 'Dispatchable Days',
            'title'    => 'Dispatchable Days',
            'name'     => 'dispatchable_days',
            'values'   =>  Mage::getModel('adminhtml/system_config_source_locale_weekdays')->toOptionArray(),
            'required'  => true
        ));

        $fieldset->addField('last_dispatch_time', 'text', array(
            'label'    => 'Latest Dispatch Time',
            'title'    => 'Latest Dispatch Time',
            'name'     => 'last_dispatch_time',
            'required'  => true
        ));

        $fieldset->addField('estimated_delivery_from', 'text', array(
            'label'    => 'Estimated Delivery Days (Lower Bound)',
            'title'    => 'Estimated Delivery Days (Lower Bound)',
            'name'     => 'estimated_delivery_from',
            'required'  => true
        ));

        $fieldset->addField('estimated_delivery_to', 'text', array(
            'label'    => 'Estimated Delivery Days (Upper Bound)',
            'title'    => 'Estimated Delivery Days (Upper Bound)',
            'name'     => 'estimated_delivery_to',
            'required'  => true
        ));

        $fieldset->addField('deliverable_days', 'multiselect', array(
            'label'    => 'Deliverable Days',
            'title'    => 'Deliverable Days',
            'name'     => 'deliverable_days',
            'values'   =>  Mage::getModel('adminhtml/system_config_source_locale_weekdays')->toOptionArray(),
            'required'  => true
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}