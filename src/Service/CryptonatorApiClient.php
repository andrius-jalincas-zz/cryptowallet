<?php


namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class CryptonatorApiClient
 * @package App\Service
 */
class CryptonatorApiClient extends ExchangeRateApi
{
    /**
     * @param string $currencySlug
     * @return float
     * @throws TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public  function getExchangeRate(string $currencySlug): float
    {
        $client = HttpClient::create();
        $requestUrl = sprintf("https://api.cryptonator.com/api/ticker/%s-usd", strtolower($currencySlug));
        try {
            $response = $client->request("GET", $requestUrl);
        } catch (TransportExceptionInterface $exception) {
            throw new \UnexpectedValueException(
                "Couldn't connect to 3rd party api",
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        if (200 !== $response->getStatusCode()) {
            throw new \UnexpectedValueException(
                "Couldn't fetch currency rates",
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        $responseData = json_decode($response->getContent(), true);

        if (empty($responseData['ticker']['price'])) {
            throw new \UnexpectedValueException(
                "Couldn't fetch currency price",
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }


        return $responseData['ticker']['price'];
    }
}