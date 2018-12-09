<?php
declare(strict_types=1);

/**
 * Class Yireo_TaxRatesManager_Provider_OnlineRates
 */
class Yireo_TaxRatesManager_Provider_OnlineRates
{
    /**
     * @var string
     */
    private $onlineUrl;

    /**
     * Yireo_TaxRatesManager_Provider_OnlineRates constructor.
     * @param string $onlineUrl
     */
    public function __construct(string $onlineUrl)
    {
        $this->onlineUrl = $onlineUrl;
    }

    /**
     * @return Yireo_TaxRatesManager_Rate_Rate[]
     * @throws Zend_Http_Client_Exception
     */
    public function getRates(): array
    {
        require_once BP.'/vendor/autoload.php';

        $rates = [];
        $onlineRates = new Yireo\EuVatRates\MagentoRates(BP.'/var/tmp');

        if (!empty($this->onlineUrl)) {
            $onlineRates->setPath($this->onlineUrl);
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