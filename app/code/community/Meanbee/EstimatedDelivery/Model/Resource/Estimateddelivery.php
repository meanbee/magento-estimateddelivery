<?php
class Meanbee_EstimatedDelivery_Model_Resource_Estimateddelivery extends Mage_Core_Model_Resource_Db_Abstract {

    protected $_serializableFields = array(
        'deliverable_days'  => array(null, array()),
        'dispatchable_days' => array(null, array())
    );

    protected function _construct() {
        $this->_init('meanbee_estimateddelivery/estimateddelivery', 'entity_id');
    }
}
