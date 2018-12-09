<?php
declare(strict_types=1);

/**
 * Class Yireo_TaxRatesManager_Logger_Messages
 */
class Yireo_TaxRatesManager_Logger_Messages implements Yireo_TaxRatesManager_Logger_Interface
{
    /**
     * @param string $msg
     */
    public function info(string $msg)
    {
        Mage::getSingleton('adminhtml/session')->addNotice($msg);
    }

    /**
     * @param string $msg
     */
    public function success(string $msg)
    {
        Mage::getSingleton('adminhtml/session')->addNotice($msg);
    }

    /**
     * @param string $msg
     */
    public function error(string $msg)
    {
        Mage::getSingleton('adminhtml/session')->addError($msg);
    }
}
