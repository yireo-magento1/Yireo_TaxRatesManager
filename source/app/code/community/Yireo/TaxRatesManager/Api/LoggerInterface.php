<?php
declare(strict_types=1);

/**
 * Interface Yireo_TaxRatesManager_Api_LoggerInterface
 */
interface Yireo_TaxRatesManager_Api_LoggerInterface
{
    /**
     * @param string $msg
     */
    public function info(string $msg);

    /**
     * @param string $msg
     */
    public function success(string $msg);

    /**
     * @param string $msg
     */
    public function warning(string $msg);

    /**
     * @param string $msg
     */
    public function error(string $msg);
}
