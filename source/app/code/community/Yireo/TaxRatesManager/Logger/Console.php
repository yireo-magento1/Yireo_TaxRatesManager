<?php
declare(strict_types=1);

/**
 * Class Yireo_TaxRatesManager_Logger_Console
 */
class Yireo_TaxRatesManager_Logger_Console implements Yireo_TaxRatesManager_Logger_Interface
{
    /**
     * @param string $msg
     */
    public function info(string $msg)
    {
        echo $msg."\n";
    }

    /**
     * @param string $msg
     */
    public function success(string $msg)
    {
        // Ignore on purpose
    }

    /**
     * @param string $msg
     */
    public function error(string $msg)
    {
        echo "ERROR: ".$msg."\n";
    }
}
