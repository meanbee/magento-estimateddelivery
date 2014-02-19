<?php
class Meanbee_EstimatedDelivery_Model_System_Config_Source_Times {

    public function toOptionArray() {
        $times = array();

        for ($i = 0; $i < 24; $i++) {
            for ($j = 0; $j < 60; $j += 30) {
                $label = sprintf("%02d:%02d", $i, $j);
                $value = sprintf("%02d:%02d:00", $i, $j);

                $times []= array('value' => $value, "label" => $label);
            }
        }

        return $times;
    }
}
