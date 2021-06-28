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

use BitBag\SyliusAdyenPlugin\Client\AdyenClient;
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
            'payum.factory_title' => 'Adyen'
        ]);

        if (false === (bool) $config['payum.api']) {
            $config['payum.default_options'] = [
                'environment' => AdyenClientInterface::TEST_ENVIRONMENT,
            ] + AdyenClient::DEFAULT_OPTIONS;

            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = array_keys($config['payum.default_options']);

            $config['payum.api'] = function (ArrayObject $config): AdyenClientInterface {
                $config->validateNotEmpty($config['payum.required_options']);

                return new AdyenClient(
                    [
                        'apiKey' => $config['apiKey'],
                        'merchantAccount' => $config['merchantAccount'],
                        'hmacKey' => $config['hmacKey'],
                        'environment' => $config['environment'],
                        'clientKey' => $config['clientKey'],
                        'authUser' => $config['authUser'],
                        'authPassword' => $config['authPassword'],
                    ],
                    $config['httplug.client']
                );
            };
        }
    }
}
