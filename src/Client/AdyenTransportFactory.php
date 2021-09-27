<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

use Adyen\Client;
use Adyen\Environment;
use Adyen\HttpClient\ClientInterface;
use Adyen\HttpClient\CurlClient;
use BitBag\SyliusAdyenPlugin\Resolver\Configuration\ConfigurationResolver;

final class AdyenTransportFactory implements AdyenTransportFactoryInterface
{
    /** @var ClientInterface */
    private $adyenHttpClient;

    public function __construct(?ClientInterface $adyenHttpClient = null)
    {
        $this->adyenHttpClient = $adyenHttpClient ?? new CurlClient();
    }

    public function create(array $options): Client
    {
        $options = (new ConfigurationResolver())->resolve($options);

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
