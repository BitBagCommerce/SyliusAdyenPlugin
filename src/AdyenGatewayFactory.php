<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin;

use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class AdyenGatewayFactory extends GatewayFactory
{
    public const FACTORY_NAME = 'adyen';

    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => self::FACTORY_NAME,
            'payum.factory_title' => 'Adyen',
        ]);

        if (false === (bool) $config['payum.api']) {
            $config['payum.default_options'] = [
                'skinCode' => '',
                'merchantAccount' => '',
                'hmacKey' => '',
                'environment' => AdyenClientInterface::TEST_ENVIRONMENT,
                'notification_method' => 'basic',
                'default_payment_fields' => [],
                'ws_user' => '',
                'ws_user_password' => '',
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [
                'skinCode',
                'merchantAccount',
                'hmacKey',
            ];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new \stdClass(
                    [
                        'skinCode' => $config['skinCode'],
                        'merchantAccount' => $config['merchantAccount'],
                        'hmacKey' => $config['hmacKey'],
                        'notification_hmac' => $config['hmacNotification'],
                        'environment' => $config['environment'],
                        'notification_method' => $config['notification_method'],
                        'default_payment_fields' => $config['default_payment_fields'],
                        'ws_user' => $config['wsUser'],
                        'ws_user_password' => $config['wsUserPassword'],
                    ],
                    $config['payum.http_client']
                );
            };
        }
    }
}
