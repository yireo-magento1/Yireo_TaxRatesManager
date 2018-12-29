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
 * Class Yireo_TaxRatesManager_Rate_Rate
 */
class Yireo_TaxRatesManager_Rate_Rate
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $countryId;

    /**
     * @var float
     */
    private $percentage;

    /**
     * Yireo_TaxRatesManager_Rate_Rate constructor.
     * @param int $id
     * @param string $code
     * @param string $countryId
     * @param float $percentage
     */
    public function __construct(
        $id,
        $code,
        $countryId,
        $percentage)
    {
        $this->id = $id;
        $this->code = $code;
        $this->countryId = $countryId;
        $this->percentage = $percentage;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @return float
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param float $percentage
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;
    }
}
