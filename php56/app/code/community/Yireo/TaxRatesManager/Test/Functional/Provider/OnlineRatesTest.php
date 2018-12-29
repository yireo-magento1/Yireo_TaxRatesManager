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

use Yireo_TaxRatesManager_Provider_OnlineRates as Target;
use Yireo_TaxRatesManager_Test_Utils_AbstractTestCase as TestCase;

/**
 * Class Yireo_TaxRatesManager_Test_Functional_Provider_OnlineRatesTest
 */
class Yireo_TaxRatesManager_Test_Functional_Provider_OnlineRatesTest extends TestCase
{
    /**
     * Test whether the rates could be fetched in a default situation
     */
    public function testRateFetchingByDefault()
    {
        $onlineRatesProvider = $this->getTarget();
        $onlineRates = $onlineRatesProvider->getRates();
        $this->assertNotEmpty($onlineRates);
    }

    /**
     * Test whether the rates could be fetched in a default situation
     */
    public function testRateFetchingWithEmptyDefaultDefault()
    {
        $this->setConfigValue('feed_source', '');
        $onlineRatesProvider = $this->getTarget();
        $onlineRates = $onlineRatesProvider->getRates();
        $this->assertNotEmpty($onlineRates);
    }

    /**
     * Test whether the fetching crashes when an invalid alternative feed is set
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function testRateFetchingWithInvalidFeed()
    {
        $this->setConfigValue('alternative_feed_source', 'not_even_a_url');
        $onlineRatesProvider = $this->getTarget();
        $onlineRates = $onlineRatesProvider->getRates();
        $this->assertNotEmpty($onlineRates);
    }

    /**
     * @return Yireo_TaxRatesManager_Provider_OnlineRates
     */
    private function getTarget(): Target
    {
        return $this->getFactory()->get(Target::class);
    }
}
