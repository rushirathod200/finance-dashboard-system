<?php

namespace App\Support;

class Currency
{
    public static function inr(float|int|string $amount, bool $withSymbol = true): string
    {
        $amount = (float) $amount;
        $negativePrefix = $amount < 0 ? '-' : '';
        $absoluteAmount = abs($amount);
        [$whole, $decimal] = explode('.', number_format($absoluteAmount, 2, '.', ''));

        return $negativePrefix.($withSymbol ? '₹' : '').self::formatIndianDigits($whole).'.'.$decimal;
    }

    public static function indianNumber(float|int|string $amount, int $decimals = 0): string
    {
        $amount = (float) $amount;
        $negativePrefix = $amount < 0 ? '-' : '';
        $absoluteAmount = abs($amount);
        [$whole, $decimal] = array_pad(explode('.', number_format($absoluteAmount, $decimals, '.', '')), 2, null);

        $formatted = self::formatIndianDigits($whole);

        if ($decimals === 0 || $decimal === null) {
            return $negativePrefix.$formatted;
        }

        return $negativePrefix.$formatted.'.'.$decimal;
    }

    protected static function formatIndianDigits(string $whole): string
    {
        if (strlen($whole) <= 3) {
            return $whole;
        }

        $lastThree = substr($whole, -3);
        $leading = substr($whole, 0, -3);
        $leading = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $leading);

        return $leading.','.$lastThree;
    }
}
