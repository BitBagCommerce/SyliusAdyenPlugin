<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusBehatPolyfillCompilerPass implements CompilerPassInterface
{
    private const CALENDAR_CONTEXT_HOOK_ID = 'sylius.behat.context.hook.calendar';

    private const CALENDAR_CONTEXT_HOOK_CLASS = 'Sylius\Calendar\Tests\Behat\Context\Hook\CalendarContext';

    private const CALENDAR_CONTEXT_SETUP_ID = 'sylius.behat.context.setup.calendar';

    private const CALENDAR_CONTEXT_SETUP_CLASS = 'Sylius\Calendar\Tests\Behat\Context\Setup\CalendarContext';

    private const KERNEL_ENVIRONMENT_KEY = 'kernel.environment';

    private const TEST = 'test';

    public function process(ContainerBuilder $container): void
    {
        /** @var string $environment */
        $environment = $container->getParameter(self::KERNEL_ENVIRONMENT_KEY);
        if (self::TEST !== $environment) {
            return;
        }

        if (!$container->hasDefinition(self::CALENDAR_CONTEXT_HOOK_ID)) {
            $container->setAlias(self::CALENDAR_CONTEXT_HOOK_ID, self::CALENDAR_CONTEXT_HOOK_CLASS)->setPublic(true);
        }

        if (!$container->hasDefinition(self::CALENDAR_CONTEXT_SETUP_ID)) {
            $container->setAlias(self::CALENDAR_CONTEXT_SETUP_ID, self::CALENDAR_CONTEXT_SETUP_CLASS)->setPublic(true);
        }
    }
}
