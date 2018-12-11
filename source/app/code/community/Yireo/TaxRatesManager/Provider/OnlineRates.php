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
     */
    public function getRates(): array
    {
        require_once BP.'/vendor/autoload.php';

        $rates = [];
        $onlineRates = new Yireo\EuVatRates\MagentoRates(BP.'/var/cache');

        if ($this->config->allowCache() === false) {
            $onlineRates->disableCache();
        }

        $onlineUrl = $this->config->getFeedUrl();
        if (!empty($onlineUrl)) {
            $onlineRates->setPath($onlineUrl);
        }

        foreach ($onlineRates->getRates() as $onlineRate) {
            $rates[] = new Yireo_TaxRatesManager_Rate_Rate(
                0,
                (string) $onlineRate['code'],
                (string) $onlineRate['country'],
                (float) $onlineRate['rate']
            );
        }

        return $rates;
    }
}
