<?php
declare(strict_types=1);

/**
 * Class Yireo_TaxRatesManager_Provider_StoredRates
 */
class Yireo_TaxRatesManager_Provider_StoredRates
{
    /**
     * @return Yireo_TaxRatesManager_Rate_Rate[]
     */
    public function getRates(): array
    {
        $rates = [];
        $collection = Mage::getModel('tax/calculation_rate')->getCollection();

        /** @var Mage_Tax_Model_Calculation_Rate $item */
        foreach ($collection as $item) {
            $rates[] = new Yireo_TaxRatesManager_Rate_Rate(
                (int) $item->getId(),
                (string) $item->getCode(),
                (string) $item->getTaxCountryId(),
                (float) $item->getRate()
            );
        }

        return $rates;
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

        $model->setCode($rate->getCode());
        $model->setTaxCountryId($rate->getCountryId());
        $model->setRate($rate->getPercentage());
        $model->save();
    }
}
