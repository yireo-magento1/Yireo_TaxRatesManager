<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Yireo_TaxRatesManager_Util_CommandLine as Target;

class Yireo_TaxRatesManager_Test_Unit_Util_CommandLineTest extends TestCase
{
    public function testIsCli()
    {
        $target = new Target;
        $this->assertTrue($target->isCli());
    }
}
