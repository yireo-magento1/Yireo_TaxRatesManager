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
 * Class Yireo_TaxRatesManager_Config_Config
 */
class Yireo_TaxRatesManager_Config_Config
{
    const PREFIX = 'https://raw.githubusercontent.com/yireo/Magento_EU_Tax_Rates/master/';

    /**
     * @var Mage_Core_Model_App
     */
    private $app;

    /**
     * @var Yireo_TaxRatesManager_Util_CommandLine
     */
    private $cli;

    /**
     * Yireo_TaxRatesManager_Config_Config constructor.
     * @param Mage_Core_Model_App $app
     * @param Yireo_TaxRatesManager_Util_CommandLine $cli
     */
    public function __construct(
        Mage_Core_Model_App $app,
        Yireo_TaxRatesManager_Util_CommandLine $cli
    ) {
        $this->app = $app;
        $this->cli = $cli;
    }

    /**
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function fixAutomatically()
    {
        if ($this->cli->isCli()) {
            return (bool)$this->getModuleConfig('fix_automatically_in_cron');
        }

        return (bool)$this->getModuleConfig('fix_automatically_in_backend');
    }

    /**
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function sendEmail()
    {
        return (bool)$this->getModuleConfig('send_email');
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function email()
    {
        $email = (string)$this->getModuleConfig('email');
        if (!empty($email)) {
            return $email;
        }

        return (string)$this->getModuleConfig('email', 'trans_email/ident_general');
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getFeedUrl()
    {
        $alternativeFeed = (string)$this->getModuleConfig('alternative_feed_source');
        if ($alternativeFeed) {
            return $alternativeFeed;
        }

        $feed = (string)$this->getModuleConfig('feed_source');
        if (!empty($feed)) {
            return self::PREFIX . $feed;
        }

        return self::PREFIX . 'tax_rates_eu.csv';
    }

    /**
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function updateNameFromExistingItems()
    {
        return (bool)$this->getModuleConfig('update_name');
    }

    /**
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function allowCache()
    {
        if ($this->cli->isCli()) {
            return false;
        }

        return (bool)$this->getModuleConfig('cache');
    }

    /**
     * @param string $path
     * @param string $prefix
     * @return string|null
     * @throws Mage_Core_Model_Store_Exception
     */
    private function getModuleConfig($path, $prefix = '')
    {
        if (empty($prefix)) {
            $prefix = 'taxratesmanager/settings';
        }

        $prefix = preg_replace('/\/$/', '', $prefix);

        return $this->app->getStore()->getConfig($prefix . '/' . $path);
    }
}
