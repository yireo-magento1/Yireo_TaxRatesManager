<?php
class Yireo_TaxRatesManager_Util_Comparer
{
    /**
     * @param float $target
     * @param float $option1
     * @param float $option2
     * @return float
     */
    public function getSmallestDifference($target, $option1, $option2)
    {
        if (abs($target - $option1) < abs($target - $option2)) {
            return $option1;
        }

        return $option2;
    }
}
