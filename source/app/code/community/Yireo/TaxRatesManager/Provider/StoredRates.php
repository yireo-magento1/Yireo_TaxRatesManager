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
 * Class Yireo_TaxRatesManager_Provider_StoredRates
 */
class Yireo_TaxRatesManager_Provider_StoredRates
{
    /**
     * @var Yireo_TaxRatesManager_Config_Config
     */
    private $config;

    /**
     * Yireo_TaxRatesManager_Provider_StoredRates constructor.
     */
    public function __construct(
        Yireo_TaxRatesManager_Config_Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param $id
     * @return Yireo_TaxRatesManager_Rate_Rate
     */
    public function getRateById($id)
    {
        /** @var Mage_Tax_Model_Calculation_Rate $item */
        $item = Mage::getModel('tax/calculation_rate')->load($id);

        return new Yireo_TaxRatesManager_Rate_Rate(
            (int)$item->getId(),
            (string)$item->getCode(),
            (string)$item->getTaxCountryId(),
            (float)$item->getRate()
        );
    }

    /**
     * @return Yireo_TaxRatesManager_Rate_Rate[]
     */
    public function getRates()
    {
        $rates = [];
        $collection = Mage::getModel('tax/calculation_rate')->getCollection();

        /** @var Mage_Tax_Model_Calculation_Rate $item */
        foreach ($collection as $item) {
            $rates[] = new Yireo_TaxRatesManager_Rate_Rate(
                (int)$item->getId(),
                (string)$item->getCode(),
                (string)$item->getTaxCountryId(),
                (float)$item->getRate()
            );
        }

        return $rates;
    }

    /**
     * @param Yireo_TaxRatesManager_Rate_Rate $rate
     * @throws Exception
     */
    public function removeRate(Yireo_TaxRatesManager_Rate_Rate $rate)
    {
        $model = Mage::getModel('tax/calculation_rate');
        $model->load($rate->getId());
        $model->delete();
    }

    /**
     * @param Yireo_TaxRatesManager_Rate_Rate $rate
     * @throws Exception
     */
    public function saveRate(Yireo_TaxRatesManager_Rate_Rate $rate)
    {
        $model = Mage::getModel('tax/calculation_rate');

        if ($rate->getId() > 0) {
            $model->load($rate->getId());
        }

        if (!$rate->getId() > 0 || !$model->getCode() || $this->config->updateNameFromExistingItems()) {
            $model->setCode($rate->getCode());
        }

        if (!$model->getTaxPostcode()) {
            $model->setTaxPostcode('*');
        }

        $model->setTaxCountryId($rate->getCountryId());
        $model->setRate($rate->getPercentage());
        $model->save();
    }
}
