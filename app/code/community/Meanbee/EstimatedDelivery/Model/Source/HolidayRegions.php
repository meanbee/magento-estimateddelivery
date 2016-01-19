<?php
class Meanbee_EstimatedDelivery_Model_Source_HolidayRegions {
    const ENGLAND_AND_WALES = 'england-and-wales';
    const SCOTLAND          = 'scotland';
    const NORTHERN_IRELAND  = 'northern-ireland';

    public function toOptionArray() {
        $regions = array(
            array('label' => 'None',              'value' => null             ),
            array('label' => 'England and Wales', 'value' => self::ENGLAND_AND_WALES),
            array('label' => 'Scotland',          'value' => self::SCOTLAND         ),
            array('label' => 'Northern Ireland',  'value' => self::NORTHERN_IRELAND )
        );

        return $regions;
    }
}
