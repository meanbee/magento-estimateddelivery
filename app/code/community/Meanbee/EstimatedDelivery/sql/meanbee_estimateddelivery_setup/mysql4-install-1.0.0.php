<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$table = $installer->getTable('meanbee_estimateddelivery/estimateddelivery');

$sql = <<<SQL
CREATE TABLE `$table` (
    `entity_id` int(11) unsigned NOT NULL auto_increment,
    `dispatch_preparation` int(11) DEFAULT NULL,
    `dispatchable_days` varchar(256) DEFAULT NULL,
    `last_dispatch_time` time DEFAULT NULL,
    `estimated_delivery_from` int(11) DEFAULT NULL,
    `estimated_delivery_to` int(11) DEFAULT NULL,
    `deliverable_days` varchar(256) DEFAULT NULL,
    `shipping_method` varchar(256) DEFAULT NULL,
    PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;

$this->getConnection()->query($sql);

$this->endSetup();
