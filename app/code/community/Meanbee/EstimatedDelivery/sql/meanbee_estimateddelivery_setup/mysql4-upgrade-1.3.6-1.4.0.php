<?php

$installer = new Mage_Sales_Model_Resource_Setup;

$installer->startSetup();

$installer->addAttribute('quote', 'dispatch_date', array('type' => 'varchar'));
$installer->addAttribute('order', 'dispatch_date', array('type' => 'varchar'));

$installer->endSetup();
