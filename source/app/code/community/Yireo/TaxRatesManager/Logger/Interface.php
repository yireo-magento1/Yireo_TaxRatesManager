<?php
declare(strict_types=1);

/**
 * Interface Yireo_TaxRatesManager_Logger_Interface
 */
interface Yireo_TaxRatesManager_Logger_Interface
{
    /**
     * @param string $msg
     */
    public function info(string $msg);

    /**
     * @param string $msg
     */
    public function error(string $msg);
}