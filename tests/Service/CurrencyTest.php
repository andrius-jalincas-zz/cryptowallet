<?php


namespace App\Tests\Service;


use App\Service\Currency;
use App\Entity\Currency as CurrencyEntity;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        $this->service = new Currency();
    }

    /**
     * @test
     */
    public function itShouldReturnCurrencyRate()
    {
        $currency = new CurrencyEntity();
        $currency->setConverter('default');
        $currency->setName('BTC');
        $currency->setSlug('BTC');

        $result = $this->service->getValueInUsd($currency, 1);
        $this->assertIsFloat($result);
    }
}