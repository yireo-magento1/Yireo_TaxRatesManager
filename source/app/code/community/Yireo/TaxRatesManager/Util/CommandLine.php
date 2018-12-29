<?php
/**
 * Class Yireo_TaxRatesManager_Util_CommandLine
 */
class Yireo_TaxRatesManager_Util_CommandLine
{
    /**
     * @return bool
     */
    public function isCli()
    {
        return (!isset($_SERVER['SERVER_SOFTWARE']) && (php_sapi_name() == 'cli'));
    }
}
