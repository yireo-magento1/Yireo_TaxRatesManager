<?php
declare(strict_types=1);

/**
 * Class Yireo_TaxRatesManager_Config_Config
 */
class Yireo_TaxRatesManager_Config_Config
{
    /**
     * @var Mage_Core_Model_App
     */
    private $app;

    /**
     * Yireo_TaxRatesManager_Config_Config constructor.
     * @param Mage_Core_Model_App $app
     */
    public function __construct(
        Mage_Core_Model_App $app
    ) {
        $this->app = $app;
    }

    /**
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function fixAutomatically(): bool
    {
        return (bool) $this->getModuleConfig('fix_automatically');
    }

    /**
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function sendEmail(): bool
    {
        return (bool) $this->getModuleConfig('send_email');
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function email(): string
    {
        $email = (string) $this->getModuleConfig('email');
        if (!empty($email)) {
            return $email;
        }

        return (string) $this->getModuleConfig('email', 'trans_email/ident_general');
    }

    public function getFeedUrl(): string
    {
        $alternativeFeed = (string) $this->getModuleConfig('alternative_feed_source');
        if ($alternativeFeed) {
            return $alternativeFeed;
        }

        $prefix = 'https://raw.githubusercontent.com/yireo/Magento_EU_Tax_Rates/master/';
        $feed = (string) $this->getModuleConfig('feed_source');
        if (!empty($feed)) {
            return $prefix.$feed;
        }

        return $prefix.'tax_rates_eu.csv';
    }

    /**
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function updateNameFromExistingItems(): bool
    {
        return (bool) $this->getModuleConfig('update_name');
    }

    /**
     * @param string $path
     * @param string $prefix
     * @return string|null
     * @throws Mage_Core_Model_Store_Exception
     */
    private function getModuleConfig(string $path, string $prefix = '')
    {
        if (empty($prefix)) {
            $prefix = 'taxratesmanager/settings';
        }

        $prefix = preg_replace('/\/$/', '', $prefix);

        return $this->app->getStore()->getConfig($prefix.'/'.$path);
    }
}
