<?php

namespace Jetfuel\Zsagepay\Traits;

trait ConvertMoney
{
    /**
     * Convert yuan (元) to fen (分).
     *
     * @param float $amount
     * @return int
     */
    public function convertYuanToFen($amount)
    {
        return $amount * 100;
    }

    /**
     * Convert fen (分) to yuan (元).
     *
     * @param int $amount
     * @return float
     */
    public function convertFenToYuan($amount)
    {
        return $amount / 100;
    }
}
