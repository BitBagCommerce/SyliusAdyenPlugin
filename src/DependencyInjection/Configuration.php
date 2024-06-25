<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public const DEFAULT_LOGGER = 'logger';
    public const DEFAULT_PAYMENT_METHODS = [
        'scheme', 'dotpay', 'ideal', 'alipay', 'applepay', 'blik', 'amazonpay', 'sepadirectdebit',
    ];
    public const CAPTURE_METHODS = ['auto', 'delayed_manual'];
    public const DEFAULT_CAPTURE_METHOD = 'delayed_manual';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('bitbag_sylius_adyen');

        $treeBuilder
            ->getRootNode()
            ->children()
                ->arrayNode('supported_types')
                    ->ignoreExtraKeys(false)
                    ->beforeNormalization()
                        ->always(static function ($arg) {
                            return (array) $arg;
                        })
                    ->end()
                ->end()
                ->scalarNode('logger')
                    ->treatTrueLike(self::DEFAULT_LOGGER)
                    ->defaultNull()
                ->end()
                ->scalarNode('capture_method')
                    ->defaultValue(self::DEFAULT_CAPTURE_METHOD)
                    ->validate()
                        ->ifNotInArray(self::CAPTURE_METHODS)
                        ->thenInvalid('Invalid config value %s should be one of: '.implode(', ', self::CAPTURE_METHODS))
                    ->end()
                ->end()
            ->end()
            ->beforeNormalization()
            ->always(static function ($arg) {
                $arg = (array) $arg;

                if (array_key_exists('supported_types', $arg)) {
                    return $arg;
                }

                $arg['supported_types'] = self::DEFAULT_PAYMENT_METHODS;

                return $arg;
            })
            ->end()
        ;

        return $treeBuilder;
    }
}
