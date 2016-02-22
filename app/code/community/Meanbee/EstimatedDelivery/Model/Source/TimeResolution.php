<?php
class Meanbee_EstimatedDelivery_Model_Source_TimeResolution {
    const DAY   = 'day';
    const WEEK  = 'week';
    const MONTH = 'month';

    public function toOptionArray() {
        $resolutions = array(
            array('label' => 'Disabled', 'value' => null       ),
            array('label' => 'Day',      'value' => self::DAY  ),
            // array('label' => 'Week',     'value' => self::WEEK ), // Partially implemented
            array('label' => 'Month',    'value' => self::MONTH)
        );

        return $resolutions;
    }
}
