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

        $date = new Zend_Date($startDate);
        $expectedDate = new Zend_Date($expectation->getResult());
        $latestDispatchTimePredicate = $expectation->getLastDispatchTime();
        $dispatchPreparationPredicate = $expectation->getDispatchPreparation();
        $dispatchableDaysPredicate = $expectation->getDispatchableDays();

        $estimatedDeliveryInfo = Mage::getModel('meanbee_estimateddelivery/estimateddelivery')->loadByShippingMethod($shippingMethod);

        // assert predicates
        $this->assertEquals($latestDispatchTimePredicate, $estimatedDeliveryInfo->getLastDispatchTime(), sprintf("This test is predicated on '%s' having a latest dispatch time of %s. Got value %s", $shippingMethod, $latestDispatchTimePredicate, $estimatedDeliveryInfo->getLastDispatchTime()));
        $this->assertEquals($dispatchPreparationPredicate, $estimatedDeliveryInfo->getDispatchPreparation(), sprintf("This test is predicated on '%s' having a dispatch preparation of %s. Got value %s", $shippingMethod, $dispatchPreparationPredicate, $estimatedDeliveryInfo->getDispatchPreparation()));
        $this->assertEquals($dispatchableDaysPredicate, $estimatedDeliveryInfo->getDispatchableDays(), sprintf("This test is predicated on '%s' having dispatchable days %s. Got value %s", $shippingMethod, print_r($dispatchableDaysPredicate, true), print_r($estimatedDeliveryInfo->getDispatchableDays(), true)));

        $result = $this->_helper->getDispatchDate($shippingMethod, $date);
        $this->assertEquals($expectedDate, $result, sprintf("Did not get expected dispatch date for shipping method '%s'. Expected value was %s and we got %s", $shippingMethod, $expectedDate, $result));
    }

    /**
     * @dataProvider dataProvider
     * @loadExpectation
     * @loadFixture estimatedDelivery.yaml
     */
    public function testGetEstimatedDeliveryToAndFrom($testId, $startDate, $shippingMethod) {
        $expectation = $this->expected($testId);

        $date = new Zend_Date($startDate);
        $expectedFromDate = new Zend_Date($expectation->getFromDate());
        $expectedToDate = new Zend_Date($expectation->getToDate());
        $latestDispatchTimePredicate = $expectation->getLastDispatchTime();
        $dispatchPreparationPredicate = $expectation->getDispatchPreparation();
        $dispatchableDaysPredicate = $expectation->getDispatchableDays();
        $fromPredicate = $expectation->getFrom();
        $toPredicate = $expectation->getTo();
        $deliverableDaysPredicate = $expectation->getDeliverableDays();

        $estimatedDeliveryInfo = Mage::getModel('meanbee_estimateddelivery/estimateddelivery')->loadByShippingMethod($shippingMethod);

        // assert predicates
        $this->assertEquals($latestDispatchTimePredicate, $estimatedDeliveryInfo->getLastDispatchTime(), sprintf("This test is predicated on '%s' having a latest dispatch time of %s. Got value %s", $shippingMethod, $latestDispatchTimePredicate, $estimatedDeliveryInfo->getLastDispatchTime()));
        $this->assertEquals($dispatchPreparationPredicate, $estimatedDeliveryInfo->getDispatchPreparation(), sprintf("This test is predicated on '%s' having a dispatch preparation of %s. Got value %s", $shippingMethod, $dispatchPreparationPredicate, $estimatedDeliveryInfo->getDispatchPreparation()));
        $this->assertEquals($dispatchableDaysPredicate, $estimatedDeliveryInfo->getDispatchableDays(), sprintf("This test is predicated on '%s' having dispatchable days %s. Got value %s", $shippingMethod, print_r($dispatchableDaysPredicate, true), print_r($estimatedDeliveryInfo->getDispatchableDays(), true)));
        $this->assertEquals($fromPredicate, $estimatedDeliveryInfo->getEstimatedDeliveryFrom(), sprintf("This test is predicated on '%s' having a estimated delivery from of %s. Got value %s", $shippingMethod, $fromPredicate, $estimatedDeliveryInfo->getEstimatedDeliveryFrom()));
        $this->assertEquals($toPredicate, $estimatedDeliveryInfo->getEstimatedDeliveryTo(), sprintf("This test is predicated on '%s' having a estimated delivery to of %s. Got value %s", $shippingMethod, $toPredicate, $estimatedDeliveryInfo->getEstimatedDeliveryTo()));
        $this->assertEquals($deliverableDaysPredicate, $estimatedDeliveryInfo->getDeliverableDays(), sprintf("This test is predicated on '%s' having deliverable days %s. Got value %s", $shippingMethod, print_r($deliverableDaysPredicate, true), print_r($estimatedDeliveryInfo->getDeliverableDays(), true)));

        $fromDate = $this->_helper->getEstimatedDeliveryFrom($shippingMethod, $date);
        $toDate = $this->_helper->getEstimatedDeliveryTo($shippingMethod, $date);

        $this->assertEquals($expectedFromDate, $fromDate, sprintf("Did not get expected from date for shipping method '%s'. Expected value was %s and we got %s", $shippingMethod, $expectedFromDate, $fromDate));
        $this->assertEquals($expectedToDate, $toDate, sprintf("Did not get expected to date for shipping method '%s'. Expected value was %s and we got %s", $shippingMethod,  $expectedToDate, $toDate));
    }
}