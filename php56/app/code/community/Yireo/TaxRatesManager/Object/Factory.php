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
use Yireo_TaxRatesManager_Provider_StoredRates as StoredRatesProvider;
use Yireo_TaxRatesManager_Object_Manager as ObjectManager;

/**
 * Class Yireo_TaxRatesManager_Object_Factory
 *
 * Factory of singletons
 */
class Yireo_TaxRatesManager_Object_Factory
{
    /**
     * @var Yireo_TaxRatesManager_Object_Factory
     */
    private static $instance;

    /**
     * @var array
     */
    private static $instances = [];

    /**
     * @return Yireo_TaxRatesManager_Object_Factory
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param $className
     * @return mixed
     */
    public function getSingleton($className)
    {
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new $className;
        }

        return self::$instances[$className];
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->get(Config::class);
    }

    /**
     * @param string $type
     * @return Yireo_TaxRatesManager_Api_LoggerInterface
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getLogger($type = 'console')
    {
        return $this->get(Yireo_TaxRatesManager_Api_LoggerInterface::class);
    }

    /**
     * @return Yireo_TaxRatesManager_Check_Check
     */
    public function getCheck()
    {
        return $this->get(Yireo_TaxRatesManager_Check_Check::class);
    }

    /**
     * @return Yireo_TaxRatesManager_Provider_StoredRates
     */
    public function getStoredRatesProvider()
    {
        return $this->get(StoredRatesProvider::class);
    }

    /**
     * @param string $className
     * @return object
     */
    public function get($className)
    {
        return $this->getSingleton(ObjectManager::class)->get($className);
    }
}
