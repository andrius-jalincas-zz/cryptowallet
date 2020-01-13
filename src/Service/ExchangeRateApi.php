<?php


namespace App\Service;


/**
 * Class ExchangeRateApi
 * @package App\Service
 */
abstract class ExchangeRateApi
{
    /**
     * @param string $currencySlug
     * @return float
     */
    abstract public function getExchangeRate(string $currencySlug): float;
}