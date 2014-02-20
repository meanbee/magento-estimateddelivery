<?php

class Meanbee_EstimatedDelivery_Block_Adminhtml_Estimateddelivery_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    const NUMBER_OF_SHIPPING_METHODS_TO_SHOW = 5;

    public function __construct($attributes = array()) {
        parent::__construct($attributes);

        $this->setId('meanbee_estimateddelivery_grid');
        $this->setSaveParametersInSession(false);
    }

    protected function _prepareCollection() {
        $this->setCollection(Mage::getModel('meanbee_estimateddelivery/estimateddelivery')->getCollection());

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('shipping_methods', array(
            'header'    => 'Shipping Method(s)',
            'align'     => 'left',
            'frame_callback'    => array($this, 'formatShippingMethods'),
            'index'     => 'shipping_methods',
        ));

        $this->addColumn('dispatch_preparation', array(
            'header'    => 'Dispatch Preparation Time (Days)',
            'align'     => 'left',
            'index'     => 'dispatch_preparation',
            'type'      => 'number'
        ));

        $this->addColumn('dispatchable_days', array(
            'header'    => 'Dispatchable Days',
            'align'     => 'left',
            'index'     => 'dispatchable_days',
            'frame_callback'    => array($this, 'formatDays'),
            'filter'    => false
        ));

        $this->addColumn('last_dispatch_time', array(
            'header'    => 'Latest Dispatch Time',
            'align'     => 'left',
            'index'     => 'last_dispatch_time',
        ));

        $this->addColumn('estimated_delivery_from', array(
            'header'    => 'Estimated Delivery Days (Lower Bound)',
            'align'     => 'left',
            'index'     => 'estimated_delivery_from',
            'type'      => 'number'
        ));

        $this->addColumn('estimated_delivery_to', array(
            'header'    => 'Estimated Delivery Days (Upper Bound)',
            'align'     => 'left',
            'index'     => 'estimated_delivery_to',
            'type'      => 'number'
        ));

        $this->addColumn('deliverable_days', array(
            'header'    => 'Dispatch Preparation Time (days)',
            'align'     => 'left',
            'index'     => 'deliverable_days',
            'frame_callback'    => array($this, 'formatDays'),
            'filter'    => false
        ));

        return parent::_prepareColumns();
    }

    public function formatDays($value, $row, $column, $isExport) {
        $days = Mage::getModel('adminhtml/system_config_source_locale_weekdays')->toOptionArray();
        $selectedDays = array();

        foreach ($value as $index) {
            $selectedDays []= $days[$index]['label'];
        }

        return implode(', ', $selectedDays);
    }

    public function formatShippingMethods($methods, $row, $column, $isExport) {
        if (count($methods) > self::NUMBER_OF_SHIPPING_METHODS_TO_SHOW) {
            $methods = array_slice($methods, 0, self::NUMBER_OF_SHIPPING_METHODS_TO_SHOW);
            return implode(",<br/>", $methods) . ",<br/>...";
        }

        return implode(",<br/>", $methods);
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array(
            'id'=> $row->getId())
        );
    }
}
