<?php
class Meanbee_EstimatedDelivery_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case {


    /** @var  $_helper Meanbee_EstimatedDelivery_Helper_Data */
    protected $_helper;

    public function __construct($name = NULL, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->_helper = Mage::helper('meanbee_estimateddelivery');
    }

    /**
     * @loadFixture estimatedDelivery.yaml
     */
    public function testGetDispatchDate() {
        // 'test_shipping1' has a dispatch preparation of 1, ensure that this increments the day by one when time is
        // before the last dispatch date at 12:00
        $date = new Zend_Date('3rd March 2014 14:00');
        $expectedDate = new Zend_Date('4th March 2014 14:00');
        $shippingMethod = 'test_shipping1';
        $estimatedDeliveryInfo = Mage::getModel('meanbee_estimateddelivery/estimateddelivery')->loadByShippingMethod($shippingMethod);
        $this->assertEquals(1, $estimatedDeliveryInfo->getDispatchPreparation(), sprintf("This test is predicated on '%s' having a dispatch preparation of %s. Got value %s", $shippingMethod, 1, $estimatedDeliveryInfo->getDispatchPreparation()));
        $result = $this->_helper->getDispatchDate($shippingMethod, $date);
        $this->assertEquals($expectedDate, $result, sprintf("Did not get expected dispatch date for shipping method '%s'. Expected value was %s and we got %s", $shippingMethod, $expectedDate, $result));

        // 'test_shipping2' has a dispatch preparation of 0. Ensure the dispatch date is the same as the intial date.
        $date = new Zend_Date('3rd March 2014 14:00');
        $shippingMethod = 'test_shipping2';
        $estimatedDeliveryInfo = Mage::getModel('meanbee_estimateddelivery/estimateddelivery')->loadByShippingMethod($shippingMethod);
        $this->assertEquals(0, $estimatedDeliveryInfo->getDispatchPreparation(), sprintf("This test is predicated on '%s' having a dispatch preparation of %s. Got value %s", $shippingMethod, 0, $estimatedDeliveryInfo->getDispatchPreparation()));
        $result = $this->_helper->getDispatchDate($shippingMethod, $date);
        $this->assertEquals($date, $result, sprintf("Did not get expected dispatch date for shipping method '%s'. Expected value was %s and we got %s", $shippingMethod, $date, $result));
        
        // Again, using 'test_shipping2' I want to ensure that the latest delivery (12:00) is honoured
        $date = new Zend_Date('3rd March 2014 16:00');
        $expectedDate = new Zend_Date('4th March 2014 16:00');
        $shippingMethod = 'test_shipping2';
        $estimatedDeliveryInfo = Mage::getModel('meanbee_estimateddelivery/estimateddelivery')->loadByShippingMethod($shippingMethod);
        $this->assertEquals("15:00:00", $estimatedDeliveryInfo->getLastDispatchTime(), sprintf("This test is predicated on '%s' having a dispatch preparation of %s. Got value %s", $shippingMethod, "15:00:00", $estimatedDeliveryInfo->getLastDispatchTime()));
        $result = $this->_helper->getDispatchDate($shippingMethod, $date);
        $this->assertEquals($expectedDate, $result, sprintf("Did not get expected dispatch date for shipping method '%s'. Expected value was %s and we got %s", $shippingMethod, $expectedDate, $result));

        // Test to see that these two factor stack using 'test_shipping1'
        $date = new Zend_Date('3rd March 2014 16:00');
        $expectedDate = new Zend_Date('5th March 2014 16:00');
        $shippingMethod = 'test_shipping1';
        $estimatedDeliveryInfo = Mage::getModel('meanbee_estimateddelivery/estimateddelivery')->loadByShippingMethod($shippingMethod);
        $this->assertEquals("15:00:00", $estimatedDeliveryInfo->getLastDispatchTime(), sprintf("This test is predicated on '%s' having a dispatch preparation of %s. Got value %s", $shippingMethod, "15:00:00", $estimatedDeliveryInfo->getLastDispatchTime()));
        $this->assertEquals(1, $estimatedDeliveryInfo->getDispatchPreparation(), sprintf("This test is predicated on '%s' having a dispatch preparation of %s. Got value %s", $shippingMethod, 1, $estimatedDeliveryInfo->getDispatchPreparation()));
        $result = $this->_helper->getDispatchDate($shippingMethod, $date);
        $this->assertEquals($expectedDate, $result, sprintf("Did not get expected dispatch date for shipping method '%s'. Expected value was %s and we got %s", $shippingMethod, $expectedDate, $result));
    }
}