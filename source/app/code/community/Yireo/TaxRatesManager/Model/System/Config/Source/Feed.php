<?php
/**
 * Yireo TaxRatesManager extension for Magento 1
 *
 * @package     Yireo_TaxRatesManager
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2018 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class Yireo_TaxRatesManager_Model_System_Config_Source_Feed
 */
class Yireo_TaxRatesManager_Model_System_Config_Source_Feed
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->getSources() as $source) {
            $options[] = [
                'value' => $source,
                'label' => $source,
            ];
        }

        return $options;
    }

    /**
     * @return string[]
     */
    private function getSources()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,Yireo_TaxRatesManager_Config_Config::PREFIX.'/feeds.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $contents = curl_exec($ch);
        curl_close ($ch);

        try {
            $data = Zend_Json::decode($contents, true);
        } catch (Zend_Json_Exception $exception) {
            return [];
        }

        return $data['feeds'];
    }
}
