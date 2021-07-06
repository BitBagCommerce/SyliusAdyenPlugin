<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

use Adyen\Client;
use Adyen\Environment;
use Adyen\HttpClient\ClientInterface;
use Adyen\HttpClient\CurlClient;
use Webmozart\Assert\Assert;

class AdyenTransportFactory
{
    /** @var ClientInterface */
    private $adyenHttpClient;

    public function __construct(?ClientInterface $adyenHttpClient = null)
    {
        $this->adyenHttpClient = $adyenHttpClient ?? new CurlClient();
    }

    public function create(array $options): Client
    {
        Assert::keyExists($options, 'apiKey');
        Assert::keyExists($options, 'environment');

        $client = new Client();
        $client->setHttpClient($this->adyenHttpClient);

        $client->setXApiKey($options['apiKey']);
        $client->setEnvironment(
            $options['environment'] == AdyenClientInterface::TEST_ENVIRONMENT
                ? Environment::TEST
                : Environment::LIVE
        );
        $client->setTimeout(30);

        return $client;
    }
}
