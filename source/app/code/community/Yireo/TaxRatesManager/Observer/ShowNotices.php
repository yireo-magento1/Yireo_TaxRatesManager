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

/**
 * Class Yireo_TaxRatesManager_Observer_ShowNotices
 */
class Yireo_TaxRatesManager_Observer_ShowNotices
{
    /**
     * @var Yireo_TaxRatesManager_Object_Factory
     */
    private $factory;

    /**
     * @var Mage_Core_Model_Store
     */
    private $store;

    /**
     * @var bool
     */
    static private $isWarned = false;

    /**
     * Yireo_TaxRateManager_Observer_ShowNotices constructor.
     * @throws Mage_Core_Model_Store_Exception
     */
    public function __construct()
    {
        $this->factory = Yireo_TaxRatesManager_Object_Factory::getInstance();
        $this->store = Mage::app()->getStore();
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws Zend_Http_Client_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function execute(Varien_Event_Observer $observer)
    {
        if ($this->store->isAdmin() == false) {
            return $this;
        }

        $request = Mage::app()->getRequest();
        $controllerName = $request->getControllerName();
        if ($controllerName !== 'tax_rate') {
            return $this;
        }

        $check = $this->factory->getCheck();
        $check->execute();

        return $this;
    }
}