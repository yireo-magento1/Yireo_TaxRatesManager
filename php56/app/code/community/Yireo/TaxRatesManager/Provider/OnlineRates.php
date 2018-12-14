<?php
/**
 * Yireo TaxRatesManager extension for Magento 1
 *
 * @package     Yireo_TaxRatesManager
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2018 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

/**
 * Class Yireo_TaxRatesManager_Provider_OnlineRates
 */
class Yireo_TaxRatesManager_Provider_OnlineRates
{
    /**
     * @var Yireo_TaxRatesManager_Config_Config
     */
    private $config;

    /**
     * @var array
     */
    private $columns = [
        'code' => 'Code',
        'country' => 'Country',
        'state' => 'State',
        'zip' => 'Zip/Post Code',
        'rate' => 'Rate',
        'zip_is_range' => 'Zip/Post is Range',
        'range_from' => 'Range From',
        'range_to' => 'Range To',
        'default' => 'default'
    ];

    /**
     * Yireo_TaxRatesManager_Provider_OnlineRates constructor.
     * @param Yireo_TaxRatesManager_Config_Config $config
     */
    public function __construct(
        Yireo_TaxRatesManager_Config_Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @return Yireo_TaxRatesManager_Rate_Rate[]
     * @throws Mage_Core_Model_Store_Exception
     * @throws Zend_Cache_Exception
     */
    public function getRates(): array
    {
        require_once BP . '/vendor/autoload.php';

        $rates = [];
        $onlineRates = $this->getRatesFromCacheOrOnline();

        foreach ($onlineRates as $onlineRate) {
            $rates[] = new Yireo_TaxRatesManager_Rate_Rate(
                0,
                (string)$onlineRate['code'],
                (string)$onlineRate['country'],
                (float)$onlineRate['rate']
            );
        }

        return $rates;
    }

    /**
     * @return array
     * @throws Mage_Core_Model_Store_Exception
     * @throws Zend_Cache_Exception
     */
    private function getRatesFromCacheOrOnline(): array
    {
        if ($this->config->allowCache()) {
            $rates = $this->loadFromCache();
            if (!empty($rates)) {
                return $rates;
            }
        }

        $rates = $this->loadFromOnline();
        $this->saveToCache($rates);

        return $rates;
    }

    /**
     * @return array
     * @throws Mage_Core_Model_Store_Exception
     */
    private function loadFromCache(): array
    {
        if ($data = Mage::app()->getCache()->load($this->getCacheId())) {
            $data = unserialize($data);
        }

        if (!empty($data) && is_array($data)) {
            return $data;
        }

        return [];
    }

    /**
     * @return array
     * @throws Mage_Core_Model_Store_Exception
     */
    private function loadFromOnline(): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->config->getFeedUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $contents = curl_exec($ch);
        curl_close ($ch);

        $rows = array_map('str_getcsv', explode("\n", $contents));
        $headerRow = array_shift($rows);
        $this->validateHeaderRow($headerRow);

        foreach ($rows as $row) {
            if (empty($row[0])) {
                continue;
            }
            $i = 0;
            $rate = [];
            foreach ($this->columns as $columnCode => $columnName) {
                $rate[$columnCode] = $row[$i] ?? '';
                $i++;
            }
            $rates[] = $rate;
        }

        return $rates;
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    private function getCacheId(): string
    {
        $feedUrl = $this->config->getFeedUrl();
        return 'TAXRATESMANAGER_' . md5($feedUrl);
    }

    /**
     * @param $data
     * @throws Mage_Core_Model_Store_Exception
     * @throws Zend_Cache_Exception
     */
    private function saveToCache($data)
    {
        $cacheTags = [];
        Mage::app()->getCache()->save(serialize($data), $this->getCacheId(), $cacheTags);
    }

    /**
     * @param array $headerRow
     * @return bool
     */
    private function validateHeaderRow(array $headerRow): bool
    {
        if (count($headerRow) !== count($this->columns)) {
            throw new InvalidArgumentException('CSV header is of unexpected size: '.var_export($headerRow));
        }

        $i = 0;
        foreach ($this->columns as $columnCode => $columnName) {
            if ($headerRow[$i] != $columnName) {
                throw new InvalidArgumentException(sprintf('CSV header contains unexpected value "%s" at position %d',
                    $headerRow[$i], $i));
            }
            $i++;
        }

        return true;
    }
}
