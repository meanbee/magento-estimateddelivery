<?php
class Meanbee_EstimatedDelivery_Model_Source_HolidayRegions {
    const ENGLAND_AND_WALES = 'england-and-wales';
    const SCOTLAND          = 'scotland';
    const NORTHERN_IRELAND  = 'northern-ireland';
    // The following are used later for getStoreConfig so must match name of the fields defined in system.xml
    const LIST1             = 'holiday_list_1';
    const LIST2             = 'holiday_list_2';
    const LIST3             = 'holiday_list_3';
    const LIST4             = 'holiday_list_4';

    public function toOptionArray() {
        $regions = array(
            array('label' => 'None',              'value' => null             ),
            array('label' => 'England and Wales', 'value' => self::ENGLAND_AND_WALES),
            array('label' => 'Scotland',          'value' => self::SCOTLAND         ),
            array('label' => 'Northern Ireland',  'value' => self::NORTHERN_IRELAND ),
            array('label' => 'Holiday List 1',    'value' => self::LIST1 ),
            array('label' => 'Holiday List 2',    'value' => self::LIST2 ),
            array('label' => 'Holiday List 3',    'value' => self::LIST3 ),
            array('label' => 'Holiday List 4',    'value' => self::LIST4 ),
        );

        return $regions;
    }
}
