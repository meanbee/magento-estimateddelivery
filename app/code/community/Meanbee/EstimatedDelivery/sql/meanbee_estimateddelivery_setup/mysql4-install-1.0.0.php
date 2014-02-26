<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$mainTable = $installer->getTable('meanbee_estimateddelivery/estimateddelivery');
$methodTable = $installer->getTable('meanbee_estimateddelivery/estimateddelivery_method');

$mainTableSql = <<<SQL
CREATE TABLE `$mainTable` (
    `entity_id` int(11) unsigned NOT NULL auto_increment,
    `dispatch_preparation` int(11) DEFAULT NULL,
    `dispatchable_days` varchar(256) DEFAULT NULL,
    `last_dispatch_time` time DEFAULT NULL,
    `estimated_delivery_from` int(11) DEFAULT NULL,
    `estimated_delivery_to` int(11) DEFAULT NULL,
    `deliverable_days` varchar(256) DEFAULT NULL,
    PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;

$methodTableSql = <<<SQL
CREATE TABLE `$methodTable` (
    `shipping_method` varchar(64) NOT NULL,
    `estimated_delivery_id` int(11) unsigned NOT NULL,
    PRIMARY KEY (`shipping_method`,`estimated_delivery_id`),
    CONSTRAINT `FK_estimated_delivery_id` FOREIGN KEY (`estimated_delivery_id`) REFERENCES `$mainTable` (`entity_id`) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;


$this->getConnection()->query($mainTableSql);
$this->getConnection()->query($methodTableSql);

$this->endSetup();
