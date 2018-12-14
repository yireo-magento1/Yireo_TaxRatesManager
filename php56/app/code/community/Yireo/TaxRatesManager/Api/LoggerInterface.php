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
 * Interface Yireo_TaxRatesManager_Api_LoggerInterface
 */
interface Yireo_TaxRatesManager_Api_LoggerInterface
{
    /**
     * @param string $msg
     */
    public function info($msg);

    /**
     * @param string $msg
     */
    public function success($msg);

    /**
     * @param string $msg
     */
    public function warning($msg);

    /**
     * @param string $msg
     */
    public function error($msg);
}
