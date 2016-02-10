<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$mainTable = $installer->getTable('meanbee_estimateddelivery/estimateddelivery');

$sql = <<<EOQ

ALTER TABLE `{$mainTable}` ADD `select_slot_resolution` VARCHAR(256) DEFAULT NULL;

EOQ;

$installer->run($sql);

$installer->endSetup();
