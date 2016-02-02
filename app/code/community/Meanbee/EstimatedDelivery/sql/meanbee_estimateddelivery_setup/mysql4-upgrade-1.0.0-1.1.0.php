<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$mainTable = $installer->getTable('meanbee_estimateddelivery/estimateddelivery');

$sql = <<<EOQ

ALTER TABLE `{$mainTable}` ADD `dispatch_time_holidays` VARCHAR(256) DEFAULT NULL AFTER `dispatch_preparation`;
ALTER TABLE `{$mainTable}` ADD `dispatch_day_holidays` VARCHAR(256) DEFAULT NULL AFTER `dispatchable_days`;
ALTER TABLE `{$mainTable}` ADD `delivery_time_holidays` VARCHAR(256) DEFAULT NULL AFTER `estimated_delivery_to`;
ALTER TABLE `{$mainTable}` ADD `delivery_day_holidays` VARCHAR(256) DEFAULT NULL AFTER `deliverable_days`;

EOQ;

$installer->run($sql);

$installer->endSetup();
