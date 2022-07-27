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
use Psr\Log\LoggerInterface;

final class AdyenTransportFactory implements AdyenTransportFactoryInterface
{
    /** @var ClientInterface */
    private $adyenHttpClient;

    /** @var LoggerInterface|null */
    private $logger;

    public function __construct(
        ?LoggerInterface $logger = null,
        ?ClientInterface $adyenHttpClient = null
    ) {
        $this->logger = $logger;
        $this->adyenHttpClient = $adyenHttpClient ?? new CurlClient();
    }

    public function create(array $options): Client
    {
        $options = (new ConfigurationResolver())->resolve($options);

        $client = new Client();
        $client->setHttpClient($this->adyenHttpClient);

        if (null !== $this->logger) {
            $client->setLogger($this->logger);
        }

        $client->setXApiKey($options['apiKey']);
        $client->setEnvironment(
            AdyenClientInterface::TEST_ENVIRONMENT == $options['environment']
                ? Environment::TEST
                : Environment::LIVE
        );
        $client->setTimeout(30);

        return $client;
    }
}
