<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Resolver\Configuration;

use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationResolver
{
    public function resolve(array $configuredOptions): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'apiKey' => '',
            'clientKey' => '',
            'hmacKey' => '',
            'authUser' => '',
            'authPassword' => '',
            'environment' => AdyenClientInterface::TEST_ENVIRONMENT,
            'adyen' => 0,
            'merchantAccount' => ''
        ]);
        $resolver->setRequired($resolver->getDefinedOptions());

        return $resolver->resolve($configuredOptions);
    }
}
