<?php
class Meanbee_EstimatedDelivery_Model_Resource_Estimateddelivery_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    protected $_serializedFields = array('dispatchable_days', 'deliverable_days');

    protected function _construct() {
        $this->_init('meanbee_estimateddelivery/estimateddelivery');
    }

    protected function _afterLoad() {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            foreach ($this->_serializedFields as $field) {
                $unserializedData = $item->getData($field);
                $item->setData($field, unserialize($unserializedData));
            }
        }
        return $this;
    }
}
