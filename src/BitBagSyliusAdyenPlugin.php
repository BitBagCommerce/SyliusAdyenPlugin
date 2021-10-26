<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BitBagSyliusAdyenPlugin extends Bundle
{
    const TAG_FALLBACK = [
        'sylius.command_bus' => 'sylius_default.bus',
        'sylius.event_bus' => 'sylius_event.bus'
    ];

    use SyliusPluginTrait;

    public function getContainerExtension(): ?ExtensionInterface
    {
        $this->containerExtension = $this->createContainerExtension() ?? false;

        return $this->containerExtension !== false ? $this->containerExtension : null;
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new class implements CompilerPassInterface{

            public function process(ContainerBuilder $container)
            {
                $buses = array_keys($container->findTaggedServiceIds('messenger.bus'));

                $handlers = $container->findTaggedServiceIds('bitbag.sylius_adyen_plugin.command_bus');
                $container->setAlias('bitbag.sylius_adyen_plugin.command_bus', 'sylius_default.bus');

                foreach ($handlers as $handler=>$tagData) {
                    $def = $container->findDefinition($handler);
                    $busName = $tagData[0]['bus'] ?? 'sylius.command_bus';
                    $def->addTag('messenger.message_handler', [
                        'bus' => in_array($busName, $buses, true) ? $busName : BitBagSyliusAdyenPlugin::TAG_FALLBACK[$busName]
                    ]);
                }
            }
        }, PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
    }


}
