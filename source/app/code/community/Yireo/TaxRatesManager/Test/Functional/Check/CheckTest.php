<?php
/**
 * Yireo TaxRatesManager extension for Magento 1
 *
 * @package     Yireo_TaxRatesManager
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2018 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Class Yireo_TaxRatesManager_Test_Functional_Check_CheckTest
 */
class Yireo_TaxRatesManager_Test_Functional_Check_CheckTest extends TestCase
{
    /**
     *
     */
    public function testCurrentSituation()
    {
        $factory = $this->getFactory();
        $check = $factory->getCheck();

        $this->assertTrue(true);
    }

    /**
     * @return Yireo_TaxRatesManager_Object_Factory
     */
    private function getFactory(): Yireo_TaxRatesManager_Object_Factory
    {
        return Yireo_TaxRatesManager_Object_Factory::getInstance();
    }
}