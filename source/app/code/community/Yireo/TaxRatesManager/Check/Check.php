<?php
declare(strict_types=1);

use Yireo_TaxRatesManager_Config_Config as Config;
use Yireo_TaxRatesManager_Api_LoggerInterface as Logger;
use Yireo_TaxRatesManager_Provider_OnlineRates as OnlineRatesProvider;
use Yireo_TaxRatesManager_Provider_StoredRates as StoredRatesProvider;
use Yireo_TaxRatesManager_Rate_Rate as Rate;

/**
 * Class Yireo_TaxRatesManager_Check_Check
 */
class Yireo_TaxRatesManager_Check_Check
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var OnlineRatesProvider
     */
    private $onlineRatesProvider;

    /**
     * @var StoredRatesProvider
     */
    private $storedRatesProvider;

    /**
     * @var int
     */
    private $verbosity;

    /**
     * Yireo_TaxRatesManager_Provider constructor.
     * @param Config $config
     * @param Logger $logger
     * @param string $onlineRatesUrl
     * @param int $verbosity
     */
    public function __construct(
        Config $config,
        Logger $logger,
        string $onlineRatesUrl = 'https://raw.githubusercontent.com/yireo/Magento_EU_Tax_Rates/master/tax_rates_eu.csv',
        int $verbosity = 0
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->onlineRatesProvider = new OnlineRatesProvider($onlineRatesUrl);
        $this->storedRatesProvider = new StoredRatesProvider($config);
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
     * @param Rate[] $storedRates
     * @param Rate[] $onlineRates
     * @throws Mage_Core_Model_Store_Exception
     */
    private function checkMatches(array $storedRates, array $onlineRates)
    {
        foreach ($storedRates as $storedRate) {
            $this->checkStoredRate($storedRate, $onlineRates);
        }

        foreach ($onlineRates as $onlineRate) {
            $this->checkOnlineRate($onlineRate, $storedRates);
        }
    }

    /**
     * @param Rate $storedRate
     * @param Rate[] $onlineRates
     * @return bool
     */
    private function checkStoredRate(Rate $storedRate, array $onlineRates)
    {
        $suggestRate = 0;
        foreach ($onlineRates as $onlineRate) {
            if ($onlineRate->getCountryId() !== $storedRate->getCountryId()) {
                continue;
            }

            $suggestRate = $this->getSuggestedRate($storedRate->getPercentage(), $onlineRate->getPercentage(),
                $suggestRate);

            if ($this->verbosity >= 2) {
                $this->logger->info('Comparing ' . $onlineRate->getCountryId() . ' ' . $onlineRate->getPercentage() . ' with ' . $storedRate->getPercentage());
            }

            if ($onlineRate->getPercentage() !== $storedRate->getPercentage()) {
                continue;
            }

            return true;
        }


        $msg = sprintf('Existing rate "%s" (%s%%) seems incorrect.', $storedRate->getCode(), $storedRate->getPercentage());

        if ($suggestRate > 0) {
            $msg .= ' ' . sprintf('Perhaps it should be %s%%?', $suggestRate);
        } else {
            $msg .= ' Perhaps it should be removed or empty?';
        }

        $this->logger->warning($msg);
    }

    /**
     * @param Rate $onlineRate
     * @param Rate[] $storedRates
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    private function checkOnlineRate(Rate $onlineRate, array $storedRates): bool
    {
        if (!$onlineRate->getPercentage() > 0) {
            return false;
        }

        $foundMatch = false;

        foreach ($storedRates as $storedRate) {
            if ($storedRate->getCountryId() !== $onlineRate->getCountryId()) {
                continue;
            }

            if ($storedRate->getPercentage() !== $onlineRate->getPercentage()) {
                continue;
            }

            $foundMatch = true;
        }

        if ($foundMatch) {
            return false;
        }

        $this->logger->warning(sprintf('The rate "%s" (%s%%) is not configured in your store yet [%s]',
            $onlineRate->getCode(),
            $onlineRate->getPercentage(),
            $onlineRate->getCountryId()
        ));

        if ($this->config->fixAutomatically()) {
            $this->storedRatesProvider->saveRate($onlineRate);
            $msg = sprintf('Automatically saved a new rate %s: %s', $onlineRate->getPercentage(), $onlineRate->getCode());
            $this->logger->success($msg);
        }

        return true;
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
