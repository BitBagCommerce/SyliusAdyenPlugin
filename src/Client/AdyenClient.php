<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Client;

use Adyen\Client;
use Adyen\Config;
use Adyen\Environment;
use Adyen\Service\Checkout;
use Payum\Core\Bridge\Spl\ArrayObject;
use Psr\Http\Client\ClientInterface;

final class AdyenClient implements AdyenClientInterface
{
    private $options = [
        'apiKey' => null,
        'skinCode' => null,
        'merchantAccount' => null,
        'hmacKey' => null,
        'environment' => 'test',
        'notification_method' => null,
        'notification_hmac' => null,
        'default_payment_fields' => [],
        'ws_user' => null,
        'ws_user_password' => null,
    ];

    /** @var ClientInterface */
    private $httpClient;

    public function __construct(
        array $options,
        ClientInterface $httpClient
    ) {
        $options = ArrayObject::ensureArrayObject($options);
        $options->defaults($this->options);
        $options->validateNotEmpty([
            'apiKey',
            /*'skinCode',*/
            'merchantAccount',
            /*'hmacKey',
            'notification_hmac',
            'ws_user',
            'ws_user_password',*/
        ]);

        $this->options = $options;
        $this->httpClient = $httpClient;
    }

    private function createClient(): Checkout
    {
        $client = new Client(new Config([
            'httpClient'=>$this->httpClient
        ]));

        $client->setXApiKey($this->options['apiKey']);
        $client->setEnvironment(
            $this->options['environment'] == 'test' ? \Adyen\Environment::TEST : Environment::LIVE
        );
        $client->setTimeout(30);

        return new Checkout($client);
    }

    public function getAvailablePaymentMethods(
        string $locale,
        string $countryCode,
        int $amount,
        string $currencyCode
    ): array {
        $payload = [
            'amount'=>[
                'value'=>$amount,
                'currency'=> $currencyCode
            ],
            'reference' => 'payment-test',
            'merchantAccount' => $this->options['merchantAccount'],
            'countryCode' => $countryCode,
            'shopperLocale' => $locale
        ];

        $result = $this->createClient()->paymentMethods($payload);

        if (!isset($result['paymentMethods'])) {
            throw new \RuntimeException(sprintf('Adyen API failed to return any payment methods'));
        }

        return array_column($result['paymentMethods'], 'name', 'type');
    }
}
