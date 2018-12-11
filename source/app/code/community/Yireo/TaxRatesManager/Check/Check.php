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
     * @var Yireo_TaxRatesManager_Util_Comparer
     */
    private $comparer;

    /**
     * @var int
     */
    private $verbosity;

    /**
     * Yireo_TaxRatesManager_Provider constructor.
     * @param Config $config
     * @param Logger $logger
     * @param Yireo_TaxRatesManager_Provider_OnlineRates $onlineRatesProvider
     * @param Yireo_TaxRatesManager_Provider_StoredRates $storedRatesProvider
     * @param Yireo_TaxRatesManager_Util_Comparer $comparer
     * @param int $verbosity
     */
    public function __construct(
        Config $config,
        Logger $logger,
        OnlineRatesProvider $onlineRatesProvider,
        StoredRatesProvider $storedRatesProvider,
        Yireo_TaxRatesManager_Util_Comparer $comparer,
        int $verbosity = 0
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->onlineRatesProvider = $onlineRatesProvider;
        $this->storedRatesProvider = $storedRatesProvider;
        $this->comparer = $comparer;
        $this->verbosity = $verbosity;
    }

    /**
     * Main function
     * @throws Zend_Http_Client_Exception
     * @throws Mage_Core_Model_Store_Exception
     * @return bool
     */
    public function __invoke(): bool
    {
        $storedRates = $this->storedRatesProvider->getRates();
        if ($this->verbosity >= 2) {
            foreach ($storedRates as $storedRate) {
                $this->logger->info('Stored rate: ' . $storedRate->getCode() . ' = ' . $storedRate->getPercentage());
            }
        }

        $onlineRates = $this->onlineRatesProvider->getRates();
        if ($this->verbosity >= 2) {
            foreach ($onlineRates as $onlineRate) {
                $this->logger->info('Online rate: ' . $onlineRate->getCode() . ' = ' . $onlineRate->getPercentage());
            }
        }

        $this->checkMatches($storedRates, $onlineRates);

        return true;
    }

    /**
     * @param int $verbosity
     */
    public function setVerbosity(int $verbosity)
    {
        $this->verbosity = $verbosity;
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
     * @throws Mage_Core_Model_Store_Exception
     */
    private function checkStoredRate(Rate $storedRate, array $onlineRates)
    {
        $suggestRate = 0;
        foreach ($onlineRates as $onlineRate) {
            if ($onlineRate->getCountryId() !== $storedRate->getCountryId()) {
                continue;
            }

            $suggestRate = $this->comparer->getSmallestDifference($storedRate->getPercentage(), $onlineRate->getPercentage(),
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

        if ($this->config->fixAutomatically()) {
            $storedRate->setPercentage($suggestRate);
            $this->storedRatesProvider->saveRate($storedRate);
            $msg = sprintf('Automatically corrected existing rate to %s%%: %s', $suggestRate, $storedRate->getCode());
            $this->logger->success($msg);
            return;
        }

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
            if ($storedRate->getCode() === $onlineRate->getCode()) {
                $foundMatch = true;
                break;
            }

            if ($storedRate->getCountryId() !== $onlineRate->getCountryId()) {
                continue;
            }

            if ($storedRate->getPercentage() !== $onlineRate->getPercentage()) {
                continue;
            }

            $foundMatch = true;
            break;
        }

        if ($foundMatch) {
            return false;
        }

        $this->logger->warning(sprintf('A new rate "%s" (%s%%) is not configured in your store yet [%s]',
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

}
