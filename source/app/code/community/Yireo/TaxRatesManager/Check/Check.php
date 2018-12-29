<?php
/**
 * Yireo TaxRatesManager extension for Magento 1
 *
 * @package     Yireo_TaxRatesManager
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2018 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

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
     * @var bool
     */
    private $fixAutomatically = false;

    /**
     * Yireo_TaxRatesManager_Provider constructor.
     * @param Config $config
     * @param Logger $logger
     * @param Yireo_TaxRatesManager_Provider_OnlineRates $onlineRatesProvider
     * @param Yireo_TaxRatesManager_Provider_StoredRates $storedRatesProvider
     * @param Yireo_TaxRatesManager_Util_Comparer $comparer
     * @param int $verbosity
     * @throws Mage_Core_Model_Store_Exception
     */
    public function __construct(
        Config $config,
        Logger $logger,
        OnlineRatesProvider $onlineRatesProvider,
        StoredRatesProvider $storedRatesProvider,
        Yireo_TaxRatesManager_Util_Comparer $comparer,
        $verbosity = 0
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->onlineRatesProvider = $onlineRatesProvider;
        $this->storedRatesProvider = $storedRatesProvider;
        $this->comparer = $comparer;
        $this->verbosity = $verbosity;
        $this->fixAutomatically = $this->config->fixAutomatically();
    }

    /**
     * Main function
     * @throws Zend_Http_Client_Exception
     * @throws Mage_Core_Model_Store_Exception
     * @throws Zend_Cache_Exception
     * @return bool
     */
    public function execute()
    {
        $storedRates = $this->storedRatesProvider->getRates();
        if (empty($storedRates)) {
            $this->logger->warning('No stored rates found');
        }

        if ($this->verbosity >= 1) {
            foreach ($storedRates as $storedRate) {
                $this->logger->info('Found stored rate: ' . $storedRate->getCode() . ' = ' . $storedRate->getPercentage());
            }
        }

        $onlineRates = $this->onlineRatesProvider->getRates();
        if (empty($storedRates)) {
            $this->logger->warning('No online rates found');
        }

        if ($this->verbosity >= 1) {
            foreach ($onlineRates as $onlineRate) {
                $this->logger->info('Found online rate: ' . $onlineRate->getCode() . ' = ' . $onlineRate->getPercentage());
            }
        }

        $this->checkMatches($storedRates, $onlineRates);

        return true;
    }

    /**
     * @param int $verbosity
     */
    public function setVerbosity($verbosity)
    {
        $this->verbosity = $verbosity;
    }

    /**
     * @param $fixAutomatically
     */
    public function setFixAutomatically($fixAutomatically)
    {
        $this->fixAutomatically = (bool) $fixAutomatically;
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
     * @throws Exception
     */
    public function checkStoredRate(Rate $storedRate, array $onlineRates)
    {
        $suggestRate = 0;
        foreach ($onlineRates as $onlineRate) {
            if ($onlineRate->getCountryId() !== $storedRate->getCountryId()) {
                continue;
            }

            $suggestRate = $this->comparer->getSmallestDifference($storedRate->getPercentage(), $onlineRate->getPercentage(),
                $suggestRate);

            if ($this->verbosity >= 2) {
                $msg = sprintf('Comparing %s rate %d%% with %d%%', $onlineRate->getCountryId(), $onlineRate->getPercentage(), $storedRate->getPercentage());
                $this->logger->info($msg);
            }

            if ($onlineRate->getPercentage() !== $storedRate->getPercentage()) {
                continue;
            }

            return true;
        }
        
        if ($suggestRate == $storedRate->getPercentage()) {
            return true;
        }
        
        if ($this->fixAutomatically) {
            $storedRate->setPercentage($suggestRate);
            $this->storedRatesProvider->saveRate($storedRate);
            $msg = sprintf('Automatically corrected existing rate to %s%%: %s', $suggestRate, $storedRate->getCode());
            $this->logger->success($msg);
            return true;
        }
        
        $msg = sprintf('Existing rate "%s" (%s%%) seems incorrect.', $storedRate->getCode(), $storedRate->getPercentage());
        if ($suggestRate > 0) {
            $msg .= ' ' . sprintf('Perhaps it should be %s%%?', $suggestRate);
        } else {
            $msg .= ' Perhaps it should be removed or empty?';
        }

        $msg .= ' ['.$storedRate->getCountryId().']';
        $fixUrl = Mage::getUrl('adminhtml/taxratesmanager/fix', ['id' => $storedRate->getId()]);
        $msg .= ' (<a href="'.$fixUrl.'">Click to fix this now</a>)';

        $this->logger->warning($msg);
        return false;
    }

    /**
     * @param Rate $onlineRate
     * @param Rate[] $storedRates
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     * @throws Exception
     */
    private function checkOnlineRate(Rate $onlineRate, array $storedRates)
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

        if ($this->fixAutomatically) {
            $this->storedRatesProvider->saveRate($onlineRate);
            $msg = sprintf('Automatically saved a new rate %s: %s', $onlineRate->getPercentage(), $onlineRate->getCode());
            $this->logger->success($msg);
        }

        return true;
    }
}
