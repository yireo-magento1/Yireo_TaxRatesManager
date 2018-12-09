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
     * @var int
     */
    private $verbosity;

    /**
     * Yireo_TaxRatesManager_Provider constructor.
     * @param Yireo_TaxRatesManager_Logger_Interface $logger
     * @param string $onlineRatesUrl
     * @param int $verbosity
     */
    public function __construct(
        Yireo_TaxRatesManager_Logger_Interface $logger,
        string $onlineRatesUrl = 'https://raw.githubusercontent.com/yireo/Magento_EU_Tax_Rates/master/tax_rates_eu.csv',
        int $verbosity = 0
    ) {
        $this->logger = $logger;
        $this->onlineRatesProvider = new Yireo_TaxRatesManager_Provider_OnlineRates($onlineRatesUrl);
        $this->storedRatesProvider = new Yireo_TaxRatesManager_Provider_StoredRates();
        $this->verbosity = $verbosity;
    }

    /**
     * Main function
     * @throws Zend_Http_Client_Exception
     * @return bool
     */
    public function __invoke(): bool
    {
        $storedRates = $this->storedRatesProvider->getRates();
        if ($this->verbosity >= 2) {
            $this->logger->info('Stored rates:');
            foreach ($storedRates as $storedRate) {
                $this->logger->info(' - ' . $storedRate->getCode() . ' = ' . $storedRate->getPercentage());
            }
        }

        $onlineRates = $this->onlineRatesProvider->getRates();
        if ($this->verbosity >= 2) {
            $this->logger->info('Online rate:');
            foreach ($onlineRates as $onlineRate) {
                $this->logger->info(' - ' . $onlineRate->getCode() . ' = ' . $onlineRate->getPercentage());
            }
        }

        $this->checkMatches($storedRates, $onlineRates);

        return true;
    }

    /**
     * @param Yireo_TaxRatesManager_Rate_Rate[] $storedRates
     * @param Yireo_TaxRatesManager_Rate_Rate[] $onlineRates
     */
    private function checkMatches(array $storedRates, array $onlineRates)
    {
        foreach ($storedRates as $storedRate) {
            $this->checkStoredRate($storedRate, $onlineRates);
        }

        foreach ($onlineRates as $onlineRate) {
            $this->checkOnlineRate($onlineRate, $storedRates);
        }

        return $this->logger->success('No issues were found');
    }

    /**
     * @param Yireo_TaxRatesManager_Rate_Rate $storedRate
     * @param Yireo_TaxRatesManager_Rate_Rate[] $onlineRates
     * @return bool
     */
    private function checkStoredRate(Yireo_TaxRatesManager_Rate_Rate $storedRate, array $onlineRates)
    {
        $suggestRate = 0;
        foreach ($onlineRates as $onlineRate) {
            if ($onlineRate->getCountryId() !== $storedRate->getCountryId()) {
                continue;
            }

            $suggestRate = $this->getSuggestedRate($storedRate->getPercentage(), $onlineRate->getPercentage(), $suggestRate);

            if ($this->verbosity >= 2) {
                $this->logger->info('NOTICE: Comparing '.$onlineRate->getCountryId().' '.$onlineRate->getPercentage().' with '.$storedRate->getPercentage());
            }

            if ($onlineRate->getPercentage() !== $storedRate->getPercentage()) {
                continue;
            }

            return true;
        }


        $msg = sprintf('Rate "%s" (%s%%) seems incorrect.', $storedRate->getCode(), $storedRate->getPercentage());

        if ($suggestRate > 0) {
            $msg .= ' ' . sprintf('Perhaps it should be %s%%?', $suggestRate);
        } else {
            $msg .= ' Perhaps it should be removed or empty?';
        }

        $this->logger->error($msg);
    }

    /**
     * @param Yireo_TaxRatesManager_Rate_Rate $onlineRate
     * @param Yireo_TaxRatesManager_Rate_Rate[] $storedRates
     */
    private function checkOnlineRate(Yireo_TaxRatesManager_Rate_Rate $onlineRate, array $storedRates)
    {
        $foundMatch = false;

        foreach ($storedRates as $storedRate) {
            if ($storedRate->getCountryId() !== $onlineRate->getCountryId()) {
                continue;
            }

            if ($storedRate->getPercentage() === $onlineRate->getPercentage()) {
                continue;
            }

            $foundMatch = true;
        }

        if (!$foundMatch) {
            $msg = sprintf('WARNING: Rate "%s" (%s%%) is not configured in your store [%s]', $onlineRate->getCode(), $onlineRate->getPercentage(), $onlineRate->getCountryId());
            $this->logger->info($msg);
        }
    }

    /**
     * @param float $oldRate
     * @param float $newRate
     * @param float $suggestedRate
     * @return float
     * @todo Improve this by checking if new diff is smaller than old diff
     */
    private function getSuggestedRate(float $oldRate, float $newRate, float $suggestedRate = 0.0): float
    {
        if ($newRate > $oldRate) {
            return $newRate;
        }

        return $suggestedRate;
    }
}
