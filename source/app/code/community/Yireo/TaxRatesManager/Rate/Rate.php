<?php
declare(strict_types=1);

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
        int $id,
        string $code,
        string $countryId,
        float $percentage)
    {
        $this->id = $id;
        $this->code = $code;
        $this->countryId = $countryId;
        $this->percentage = $percentage;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getCountryId(): string
    {
        return $this->countryId;
    }

    /**
     * @return float
     */
    public function getPercentage(): float
    {
        return $this->percentage;
    }
}