<?php


namespace App\Service;


/**
 * Class ExchangeRateApiFactory
 * @package App\Service
 */
class ExchangeRateApiFactory
{
    /**
     * @param string $converter
     * @return ExchangeRateApi
     */
    public static function getExchangeRateApi(string $converter): ExchangeRateApi
    {
        switch  ($converter) {
            default:
            case "default":
                $api = new CryptonatorApiClient();
                break;
        }

        return $api;
    }
}