<?php

class Meanbee_EstimatedDelivery_Helper_BankHoliday extends Mage_Core_Helper_Abstract
{
    const DATE_FORMAT       = '/^\d{4}-\d{2}-\d{2}$/';
    const BANK_HOLIDAY_API_ENDPOINT = 'http://www.gov.uk/bank-holidays.json';

    protected function queryAPI() {
        $result = file_get_contents(self::BANK_HOLIDAY_API_ENDPOINT);
        $result = Zend_Json::decode($result);
        return $result;
    }

    public function getBankHolidays($region) {
        if (!in_array($region, array(Meanbee_EstimatedDelivery_Model_Source_HolidayRegions::ENGLAND_AND_WALES,
                                     Meanbee_EstimatedDelivery_Model_Source_HolidayRegions::SCOTLAND,
                                     Meanbee_EstimatedDelivery_Model_Source_HolidayRegions::NORTHERN_IRELAND))) {
            Mage::log("Region is not allowed: '{$region}' passed.", Zend_Log::ERR, 'meanbee.log', true);
            return array();
        }
        if (!isset($this->_holidays)) {
            $this->_holidays = array();
        } else if (isset($this->_holidays[$region])) {
            return $this->_holidays[$region];
        }
        $data = $this->queryAPI();
        $this->_holidays[$region] = array();
        foreach ($data[$region]['events'] as $entry) {
            array_push($this->_holidays[$region], $entry['date']);
        }
        return $this->_holidays[$region];
    }

    public function isHoliday($date, $region) {
        $holidays = $this->getBankHolidays($region);
        if (is_string($date)) {
            if (preg_match(self::DATE_FORMAT, $date)) {
                return in_array($date, $holidays);
            } else {
                Mage::log('Date is malformatted, must match \'' . self::DATE_FORMAT . "', '{$date}' passed.", Zend_Log::ERR, 'meanbee.log', true);
                return false;
            }
        } elseif (is_int($date)) {
            return in_array(date('Y-m-d', $date), $holidays);
        } elseif ($date instanceof Zend_Date) {
            return in_array($date->toString('YYYY-MM-dd'), $holidays);
        } else {
            Mage::log('Date is maltyped, must be string, int or Zend_Date.', Zend_Log::ERR, 'meanbee.log', true);
            return false;
        }
    }
}
