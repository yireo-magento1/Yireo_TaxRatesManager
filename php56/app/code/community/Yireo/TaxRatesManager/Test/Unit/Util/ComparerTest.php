<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Yireo_TaxRatesManager_Util_Comparer as Target;

/**
 * Class Yireo_TaxRatesManager_Test_Unit_Util_ComparerTest
 */
class Yireo_TaxRatesManager_Test_Unit_Util_ComparerTest extends TestCase
{
    /**
     *
     */
    public function testGetSmallestDifference()
    {
        $target = new Target();
        $this->assertSame(2.0, $target->getSmallestDifference(4.0, 2.0, 7.0));
        $this->assertSame(3.0, $target->getSmallestDifference(4.0, 2.0, 3.0));
        $this->assertSame(2.0, $target->getSmallestDifference(4.0, 2.0, 2.0));
        $this->assertSame(4.0, $target->getSmallestDifference(4.0, 4.0, 2.0));
    }
}
