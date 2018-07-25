<?php
declare(strict_types=1);

/**
 * Class Yireo_TaxRatesManager_Check_Check
 */
class Yireo_TaxRatesManager_Check_Check
{
    /**
     * @var Yireo_TaxRatesManager_Logger_Console
     */
    private $logger;

    /**
     * @var Yireo_TaxRatesManager_Provider_OnlineRates
     */
    private $onlineRatesProvider;

    /**
     * @var Yireo_TaxRatesManager_Provider_StoredRates
     */
    private $storedRatesProvider;

    /**
     * Yireo_TaxRatesManager_Provider constructor.
     * @param Yireo_TaxRatesManager_Logger_Interface $logger
     * @param string $onlineRatesUrl
     */
    public function __construct(
        Yireo_TaxRatesManager_Logger_Interface $logger,
        string $onlineRatesUrl = 'https://raw.githubusercontent.com/yireo/Magento_EU_Tax_Rates/master/tax_rates_eu.csv'
    ) {
        $this->logger = $logger;
        $this->onlineRatesProvider = new Yireo_TaxRatesManager_Provider_OnlineRates($onlineRatesUrl);
        $this->storedRatesProvider = new Yireo_TaxRatesManager_Provider_StoredRates();
    }

    /**
     * Main function
     * @throws Zend_Http_Client_Exception
     * @return bool
     */
    public function execute(): bool
    {
        $storedRates = $this->storedRatesProvider->getRates();
        $onlineRates = $this->onlineRatesProvider->getRates();
        $this->checkMatches($storedRates, $onlineRates);
        // @todo: Check if $onlineRates contains new rates not yet included in $storedRates
        return true;
    }

    /**
     * @param Yireo_TaxRatesManager_Rate_Rate[] $storedRates
     * @param Yireo_TaxRatesManager_Rate_Rate[] $onlineRates
     */
    private function checkMatches($storedRates, $onlineRates)
    {
        foreach($storedRates as $storedRate) {
            $hasMatch = false;
            $suggestedRate = 0;
            foreach ($onlineRates as $onlineRate) {
                if ($onlineRate->getCountryId() !== $storedRate->getCountryId()) {
                    continue;
                }

                $originalDifference = $storedRate->getPercentage() - $suggestedRate;
                $newDifference = $storedRate->getPercentage() - $onlineRate->getPercentage();
                if ($originalDifference > $newDifference) {
                    $suggestedRate = $onlineRate->getPercentage();
                }

                if ($onlineRate->getPercentage() !== $storedRate->getPercentage()) {
                    continue;
                }

                $hasMatch = true;
                break;
            }

            if (!$hasMatch) {
                $msg = sprintf('Rate "%s" (%s%%) seems incorrect.', $storedRate->getCode(), $storedRate->getPercentage());

                if ($suggestedRate > 0) {
                    $msg .= ' '.sprintf('Perhaps it should be "%s%%"?', $suggestedRate);
                } else {
                    $msg .= ' Perhaps it should be removed?';
                }

                $this->logger->error($msg);
            }
        }
    }
}