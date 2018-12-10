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

        return $email;
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
     * @param string $pathSuffix
     * @return string|null
     * @throws Mage_Core_Model_Store_Exception
     */
    private function getModuleConfig(string $pathSuffix)
    {
        return $this->app->getStore()->getConfig('taxratesmanager/settings/'.$pathSuffix);
    }
}
