<?php

namespace Modules\Core\Services\Formatters;

class CurrencyFormatter
{
    const DECIMAL_PRECISION = 2;

    /**
     * @return string
     */
    public function getFormattedPrice($price = 0): string
    {
        return number_format((float)$price, self::DECIMAL_PRECISION, '.', '');
    }
}