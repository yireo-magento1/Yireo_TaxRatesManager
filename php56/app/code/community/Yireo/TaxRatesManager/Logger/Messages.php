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
 * Class Yireo_TaxRatesManager_Logger_Messages
 */
class Yireo_TaxRatesManager_Logger_Messages implements Yireo_TaxRatesManager_Api_LoggerInterface
{
    /**
     * @param string $msg
     */
    public function info($msg)
    {
        Mage::getSingleton('adminhtml/session')->addNotice($msg);
    }

    /**
     * @param string $msg
     */
    public function success($msg)
    {
        Mage::getSingleton('adminhtml/session')->addNotice($msg);
    }

    /**
     * @param string $msg
     */
    public function warning($msg)
    {
        Mage::getSingleton('adminhtml/session')->addWarning($msg);
    }

    /**
     * @param string $msg
     */
    public function error($msg)
    {
        Mage::getSingleton('adminhtml/session')->addError($msg);
    }
}
