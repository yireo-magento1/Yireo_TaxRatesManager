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
 * Class Yireo_TaxRatesManager_Logger_Console
 */
class Yireo_TaxRatesManager_Logger_Console implements Yireo_TaxRatesManager_Api_LoggerInterface
{
    /**
     * @param string $msg
     */
    public function info($msg)
    {
        echo "INFO: ".$msg."\n";
    }

    /**
     * @param string $msg
     */
    public function success($msg)
    {
        echo "SUCCESS: ".$msg."\n";
    }

    /**
     * @param string $msg
     */
    public function warning($msg)
    {
        echo "WARNING: ".$msg."\n";
    }

    /**
     * @param string $msg
     */
    public function error($msg)
    {
        echo "ERROR: ".$msg."\n";
    }
}
