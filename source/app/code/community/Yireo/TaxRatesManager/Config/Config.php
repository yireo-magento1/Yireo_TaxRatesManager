<?php
declare(strict_types=1);

/**
 * Class Yireo_TaxRatesManager_Config_Config
 */
class Yireo_TaxRatesManager_Config_Config
{
    /**
     * @return bool
     */
    public function getAutomaticFixing(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function getSendEmail(): bool
    {
        return true;
    }
}