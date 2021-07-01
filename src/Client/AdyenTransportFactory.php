<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

use Adyen\Client;
use Adyen\Config;
use Adyen\Environment;
use Psr\Http\Client\ClientInterface;
use Webmozart\Assert\Assert;

class AdyenTransportFactory
{
    /** @var ClientInterface */
    private $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function create(array $options): Client
    {
        Assert::keyExists($options, 'apiKey');
        Assert::keyExists($options, 'environment');

        /**
         * @phpstan-ignore-next-line
         * @psalm-suppress InvalidArgument
         */
        $client = new Client(new Config([
            'httpClient' => $this->httpClient
        ]));

        $client->setXApiKey($options['apiKey']);
        $client->setEnvironment(
            $options['environment'] == 'test' ? Environment::TEST : Environment::LIVE
        );
        $client->setTimeout(30);

        return $client;
    }
}
