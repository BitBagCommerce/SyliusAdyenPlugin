<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

final class BitBagSyliusAdyenExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    public const TRANSPORT_FACTORY_ID = 'bitbag.sylius_adyen_plugin.client.adyen_transport_factory';

    public const SUPPORTED_PAYMENT_METHODS_LIST = 'bitbag.sylius_adyen_plugin.supported_payment_methods';
    public const SUPPORTED_CAPTURE_METHODS = 'bitbag.sylius_adyen_plugin.capture_method';

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => [
                'BitBag\SyliusAdyenPlugin\Migrations' => __DIR__ . '/../Migrations',
            ],
        ]);

        $container->prependExtensionConfig('sylius_labs_doctrine_migrations_extra', [
            'migrations' => [
                'BitBag\SyliusAdyenPlugin\Migrations' => ['Sylius\Bundle\CoreBundle\Migrations', 'Sylius\RefundPlugin\Migrations'],
            ],
        ]);
    }

    public function loadInternal(array $config, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter(self::SUPPORTED_PAYMENT_METHODS_LIST, (array) $config['supported_types']);

        if (null !== $config['logger']) {
            $container->setAlias('bitbag.sylius_adyen_plugin.logger', (string) $config['logger']);
        }

        $container->setParameter(self::SUPPORTED_CAPTURE_METHODS, $config['capture_method']);

        // fallback for previous version
        if (!$container->has('sylius.command_bus')) {
            $container->setAlias('bitbag.sylius_adyen_plugin.command_bus', 'sylius_default.bus');
        }
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }

    public function getAlias(): string
    {
        return 'bitbag_sylius_adyen';
    }
}
