<?php


namespace App\Service;
use App\Entity\Currency as Entity;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class Currency
{

    /**
     * @param Entity $currency
     * @param float $amount
     * @return float
     */
    public function getValueInUsd(Entity $currency, float $amount): float
    {
        $rate = $this->getCurrencyRate($currency);

        return round($amount * $rate, 2, PHP_ROUND_HALF_DOWN);
    }

    /**
     * @param Entity $currency
     * @return float
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getCurrencyRate(Entity $currency): float
    {
        $cache = new FilesystemAdapter();
        $rate = $cache->get($currency->getSlug(), function (ItemInterface $item) use ($currency) {
            $item->expiresAfter(60);
            $exchangeRateApiClient = ExchangeRateApiFactory::getExchangeRateApi($currency->getConverter());
            $rate = $exchangeRateApiClient->getExchangeRate($currency->getSlug());

            return $rate;
        });

        return $rate;
    }
}