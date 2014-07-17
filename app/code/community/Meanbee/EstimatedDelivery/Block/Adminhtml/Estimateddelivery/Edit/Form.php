<?php
class Meanbee_EstimatedDelivery_Block_Adminhtml_Estimateddelivery_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('meanbee_estimateddelivery/estimateddelivery')->load($id)->addShippingMethods();

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $id)),
            'method' => 'post',
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('meanbee_estimateddelivery')->__('Estimated Delivery Configuration'),
            'class'     => 'fieldset-wide',
        ));

        $fieldset->addField('shipping_methods', 'multiselect', array(
            'label'    => 'Shipping Methods',
            'title'    => 'Shipping Methods',
            'name'     => 'shipping_methods',
            'values'    => $this->_getShippingMethods(),
            'required'  => true
        ));

        $fieldset->addField('dispatch_preparation', 'text', array(
            'label'    => 'Dispatch Preparation Time (Days)',
            'title'    => 'Dispatch Preparation Time (Days)',
            'name'     => 'dispatch_preparation',
            'class'     => 'validate-non-negative-number validate-digits',
            'required'  => true
        ));

        $fieldset->addField('dispatchable_days', 'multiselect', array(
            'label'    => 'Dispatchable Days',
            'title'    => 'Dispatchable Days',
            'name'     => 'dispatchable_days',
            'values'   =>  Mage::getModel('adminhtml/system_config_source_locale_weekdays')->toOptionArray(),
            'required'  => true
        ));

        $fieldset->addField('last_dispatch_time', 'select', array(
            'label'    => 'Latest Dispatch Time',
            'title'    => 'Latest Dispatch Time',
            'name'     => 'last_dispatch_time',
            'values'   => Mage::getModel('meanbee_estimateddelivery/system_config_source_times')->toOptionArray(),
            'required'  => true
        ));

        $fieldset->addField('estimated_delivery_from', 'text', array(
            'label'    => 'Estimated Delivery Days (Lower Bound)',
            'title'    => 'Estimated Delivery Days (Lower Bound)',
            'name'     => 'estimated_delivery_from',
            'class'     => 'validate-non-negative-number validate-digits',
            'required'  => true
        ));

        $fieldset->addField('estimated_delivery_to', 'text', array(
            'label'    => 'Estimated Delivery Days (Upper Bound)',
            'title'    => 'Estimated Delivery Days (Upper Bound)',
            'name'     => 'estimated_delivery_to',
            'class'     => 'validate-non-negative-number validate-digits',
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

    protected function _getShippingMethods() {
        $values = array();

        try {
            $options = Mage::getModel('adminhtml/system_config_source_shipping_allmethods')->toOptionArray();

            // Patch for matrix rates not supporting getAllowedMethods properly
            if (Mage::helper('core')->isModuleEnabled('Webshopapps_Matrixrate')) {
                $options = array_merge($options, $this->_getMatrixRatesMethods());
            }

        } catch (Exception $e) {
            return array(
                array('value'=>0,'label'=>'Unable to retreive shipping methods.'),
                array('value'=>1,'label'=>'Try going to System > Configuration'),
                array('value'=>2,'label'=>'Click the Shipping Methods tab'),
                array('value'=>3,'label'=>'then click "Save".')
            );
        }

        foreach ($options as $option) {
            if (!isset($option['value']) || !is_array($option['value'])) continue;

            foreach ($option['value'] as $value) {
                $values []= $value;
            }
        }
        return $values;
    }

    protected function _getMatrixRatesMethods() {
        if (!class_exists(Webshopapps_Matrixrate_Model_Mysql4_Carrier_Matrixrate_Collection)) {
            return array();
        }

        $collection = Mage::getResourceModel('matrixrate_shipping/carrier_matrixrate_collection')->getData();
        $options = array('matrixrate' => array('value' => array()));

        foreach ($collection as $row) {
            $options['matrixrate']['value'][] =
                    array('value' => 'matrixrate_matrixrate_' . $row['pk'],
                            'label' => '[matrixrate] MatrixRate ' . $row['pk']);

        }

        return $options;

    }
}