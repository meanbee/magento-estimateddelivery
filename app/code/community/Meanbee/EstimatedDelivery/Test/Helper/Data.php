<?php
class Meanbee_EstimatedDelivery_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case {


    /** @var  $_helper Meanbee_EstimatedDelivery_Helper_Data */
    protected $_helper;

    public function __construct($name = NULL, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->_helper = Mage::helper('meanbee_estimateddelivery');
    }

    /**
     * @dataProvider dataProvider
     * @loadExpectation
     * @loadFixture estimatedDelivery.yaml
     */
    public function testGetDispatchDate($testId, $startDate, $shippingMethod) {
        $expectation = $this->expected($testId);

        // Test to see that these two factor stack using 'test_shipping1'
        $date = new Zend_Date($startDate);
        $expectedDate = new Zend_Date($expectation->getResult());
        $latestDispatchTimePredicate = $expectation->getLastDispatchTime();
        $dispatchPreparationPredicate = $expectation->getDispatchPreparation();
        $dispatchableDaysPredicate = $expectation->getDispatchableDays();

        $estimatedDeliveryInfo = Mage::getModel('meanbee_estimateddelivery/estimateddelivery')->loadByShippingMethod($shippingMethod);

        $this->assertEquals($latestDispatchTimePredicate, $estimatedDeliveryInfo->getLastDispatchTime(), sprintf("This test is predicated on '%s' having a latest dispatch time of %s. Got value %s", $shippingMethod, $latestDispatchTimePredicate, $estimatedDeliveryInfo->getLastDispatchTime()));
        $this->assertEquals($dispatchPreparationPredicate, $estimatedDeliveryInfo->getDispatchPreparation(), sprintf("This test is predicated on '%s' having a dispatch preparation of %s. Got value %s", $shippingMethod, $dispatchPreparationPredicate, $estimatedDeliveryInfo->getDispatchPreparation()));
        $this->assertEquals($dispatchableDaysPredicate, $estimatedDeliveryInfo->getDispatchableDays(), sprintf("This test is predicated on '%s' having dispatchable days %s. Got value %s", $shippingMethod, print_r($dispatchableDaysPredicate, true), print_r($estimatedDeliveryInfo->getDispatchableDays(), true)));

        $result = $this->_helper->getDispatchDate($shippingMethod, $date);
        $this->assertEquals($expectedDate, $result, sprintf("Did not get expected dispatch date for shipping method '%s'. Expected value was %s and we got %s", $shippingMethod, $expectedDate, $result));
    }

    /**
     * @loadFixture estimatedDelivery.yaml
     */
    public function testGetEstimatedDeliveryToAndFrom() {

    }
}