<?php
namespace app\helpers;

use Yii;

class CurrencyHelper
{
    /**
     * Format amount as UGX
     * @param float $amount
     * @param bool $withSymbol
     * @return string
     */
    public static function formatUGX($amount, $withSymbol = true)
    {
        $formatted = number_format($amount, 0, '.', ',');
        return $withSymbol ? 'UGX ' . $formatted . '/=' : $formatted;
    }

    /**
     * Format amount with color based on positive/negative
     * @param float $amount
     * @param bool $isIncome
     * @return string
     */
    public static function formatColoredUGX($amount, $isIncome = true)
    {
        $class = $isIncome ? 'pos' : 'neg';
        $sign = $isIncome ? '+' : '-';
        return '<span class="tx-amt ' . $class . '">' . $sign . self::formatUGX($amount) . '</span>';
    }

    /**
     * Format amount for tables with proper alignment
     * @param float $amount
     * @return string
     */
    public static function tableUGX($amount)
    {
        return '<span style="font-family: monospace; text-align: right; display: block;">' . self::formatUGX($amount) . '</span>';
    }
}