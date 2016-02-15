<?php

/** @var Mage_Sales_Model_Mysql4_Setup $installer */
$installer = new Mage_Sales_Model_Mysql4_Setup;

$installer->startSetup();

/** @var Mage_Eav_Model_Entity_Setup $eav */
$installer->addAttribute('quote', 'delivery_slot', array('type' => 'varchar'));
$installer->addAttribute('order', 'delivery_slot', array('type' => 'varchar'));

$installer->endSetup();
