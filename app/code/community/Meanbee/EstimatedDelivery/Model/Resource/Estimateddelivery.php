<?php
class Meanbee_EstimatedDelivery_Model_Resource_Estimateddelivery extends Mage_Core_Model_Resource_Db_Abstract {

    protected $_serializableFields = array(
        'deliverable_days'  => array(null, array()),
        'dispatchable_days' => array(null, array())
    );

    protected function _construct() {
        $this->_init('meanbee_estimateddelivery/estimateddelivery', 'entity_id');
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
            $select->join('meanbee_estimateddelivery_method', 'entity_id = estimated_delivery_id', array());
            $select->group('entity_id');

            $data = $read->fetchRow($select);

            if ($data) {
                // Convert the comma separated list into an array
                $data['shipping_methods'] = explode(',', $data['shipping_methods']);
                $object->setData($data);
            }
        }

        $this->unserializeFields($object);
        $this->_afterLoad($object);

        return $this;
    }
}
