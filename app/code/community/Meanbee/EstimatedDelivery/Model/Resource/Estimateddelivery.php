<?php
class Meanbee_EstimatedDelivery_Model_Resource_Estimateddelivery extends Mage_Core_Model_Resource_Db_Abstract {

    protected $_serializableFields = array(
        'deliverable_days'  => array(null, array()),
        'dispatchable_days' => array(null, array())
    );
    protected $_methodTable;

    protected function _construct() {
        $this->_init('meanbee_estimateddelivery/estimateddelivery', 'entity_id');
        $this->_methodTable = $this->getTable('meanbee_estimateddelivery/estimateddelivery_method');
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field = null) {
        if (is_null($field)) {
            $field = $this->getIdFieldName();
        }

        $read = $this->_getReadAdapter();
        if ($read && !is_null($value)) {
            $select = $this->_getLoadSelect($field, $value, $object);

            // Add our shipping methods from another table into the load select. Retrieve them as a comma separated list.
            $groupConcat = new Zend_Db_Expr("group_concat(shipping_method separator ',')");
            $select->columns(array('shipping_methods' => $groupConcat));
            $select->joinLeft("$this->_methodTable", 'entity_id = estimated_delivery_id', array());
            $select->group('entity_id');

            $data = $read->fetchRow($select);

            if ($data['shipping_methods']) {
                // Convert the comma separated list into an array
                $data['shipping_methods'] = explode(',', $data['shipping_methods']);
            } else {
                $data['shipping_methods'] = array();
            }

            $object->setData($data);
        }

        $this->unserializeFields($object);
        $this->_afterLoad($object);

        return $this;
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        parent::_afterSave($object);

        $id = $object->getId();
        $insert = $object->getData('shipping_methods');

        $adapter = $this->_getWriteAdapter();

        // Perform deletes
        $cond = array('estimated_delivery_id=?' => $id);
        $adapter->delete($this->_methodTable, $cond);

        // Perform inserts
        if ($insert) {
            $data = array();
            foreach ($insert as $shipping_method) {
                $data[] = array(
                    'shipping_method'       => $shipping_method,
                    'estimated_delivery_id' => (int)$id
                );
            }
            $adapter->insertMultiple($this->_methodTable, $data);
        }

        return $this;
    }


}
