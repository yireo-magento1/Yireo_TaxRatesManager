<?php
declare(strict_types=1);

/**
 * Class Yireo_TaxRatesManager_Observer_ShowNotices
 */
class Yireo_TaxRatesManager_Observer_ShowNotices
{
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
        $this->store = Mage::app()->getStore();
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     * @throws Zend_Http_Client_Exception
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

        $messages = new Yireo_TaxRatesManager_Logger_Messages();
        $check = new Yireo_TaxRatesManager_Check_Check($messages);
        $check->execute();

        return $this;
    }
}