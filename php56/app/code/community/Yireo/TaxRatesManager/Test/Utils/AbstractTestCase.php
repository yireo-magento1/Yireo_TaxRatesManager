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

use PHPUnit\Framework\TestCase;

/**
 * Class Yireo_TaxRatesManager_Test_Functional_Provider_OnlineRatesProvider
 */
class Yireo_TaxRatesManager_Test_Utils_AbstractTestCase extends TestCase
{
    /**
     * Setup the configuration with default values
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    public function setUp()
    {
        parent::setUp();
        $this->setConfigValue('fix_automatically_in_backend', '0');
        $this->setConfigValue('fix_automatically_in_cron', '0');
        $this->setConfigValue('send_email', '0');
        $this->setConfigValue('email', '');
        $this->setConfigValue('feed_source', 'tax_rates_eu.csv');
        $this->setConfigValue('alternative_feed_source', '');
        $this->setConfigValue('update_name', '0');
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    public function testFactory()
    {
        $logger = $this->getFactory()->getLogger();
        $this->assertInstanceOf(Yireo_TaxRatesManager_Api_LoggerInterface::class, $logger);
    }

    /**
     * @return Yireo_TaxRatesManager_Object_Factory
     */
    protected function getFactory(): Yireo_TaxRatesManager_Object_Factory
    {
        return Yireo_TaxRatesManager_Object_Factory::getInstance();
    }

    /**
     * @return Mage_Core_Model_App
     */
    protected function getApp(): Mage_Core_Model_App
    {
        return $this->getFactory()->get(Mage_Core_Model_App::class);
    }

    /**
     * @param $name
     * @param $value
     * @param string $prefix
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function setConfigValue($name, $value, $prefix = 'taxratesmanager/settings')
    {
        $path = $prefix . '/' . $name;
        $this->getApp()->getStore()->setConfig($path, $value);
    }
}