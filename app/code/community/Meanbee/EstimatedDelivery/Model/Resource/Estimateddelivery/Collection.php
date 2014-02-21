<?php
class Meanbee_EstimatedDelivery_Model_Resource_Estimateddelivery_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    protected $_serializedFields = array('dispatchable_days', 'deliverable_days');

    protected function _construct() {
        $this->_init('meanbee_estimateddelivery/estimateddelivery');
        $this->_methodTable = $this->getTable('meanbee_estimateddelivery/estimateddelivery_method');
    }

    protected function _initSelect() {
        parent::_initSelect();

        /* Note that this approach has a maximum value of 1024 bytes, which means that if the grid wishes to show all of
        the shipping methods, rather than a subset, then this logic will need to be moved out to a separate SQL statement.
        Similar to how this is done in Meanbee_EstimatedDelivery_Model_Resource_Estimateddelivery::load */
        $groupConcat = new Zend_Db_Expr("group_concat(shipping_method separator ',')");
        $this->_select->columns(array('shipping_methods' => $groupConcat));
        $this->_select->joinLeft("$this->_methodTable", 'entity_id = estimated_delivery_id', array());
        $this->_select->group('entity_id');

        return $this;
    }


    protected function _afterLoad() {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            // Unserialize the relevant fields
            foreach ($this->_serializedFields as $field) {
                $unserializedData = $item->getData($field);
                $item->setData($field, unserialize($unserializedData));
            }

            if ($methods = $item->getShippingMethods()) {
                // Extract the shipping method data into an array
                $methods = explode(',', $methods);
            } else {
                $methods = array();
            }

            $item->setData('shipping_methods', $methods);
        }

        return $this;
    }
}
