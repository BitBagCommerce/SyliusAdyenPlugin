<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Version;

use PackageVersions\FallbackVersions;

final class VersionResolver implements VersionResolverInterface
{
    private const PACKAGE_NAME = 'bitbag/sylius-adyen-plugin';

    private const TEST_APPLICATION_VERSION = 'dev';

    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress InternalClass
     * @psalm-suppress InternalMethod
     */
    private function getPluginVersion(): string
    {
        try {
            if (class_exists(\Composer\InstalledVersions::class)) {
                return \Composer\InstalledVersions::getPrettyVersion(self::PACKAGE_NAME) ?? self::TEST_APPLICATION_VERSION;
            }

            return substr(
                FallbackVersions::getVersion(self::PACKAGE_NAME),
                0,
                -1
            );
        } catch (\Exception $ex) {
            return self::TEST_APPLICATION_VERSION;
        }
    }

    private function resolveApplicationInfo(): array
    {
        $version = '';
        if (5 === constant('Symfony\Component\HttpKernel\Kernel::MAJOR_VERSION')) {
            /** @var string $version */
            $version = constant('Sylius\Bundle\CoreBundle\Application\Kernel::VERSION');
        } elseif (defined('Sylius\Bundle\CoreBundle\SyliusCoreBundle::VERSION')) {
            /** @var string $version */
            $version = constant('Sylius\Bundle\CoreBundle\SyliusCoreBundle::VERSION');
        }

        return [
            'merchantApplication' => [
                'name' => 'adyen-sylius',
                'version' => $this->getPluginVersion(),
            ],
            'externalPlatform' => [
                'name' => 'Sylius',
                'version' => $version,
                'integrator' => 'BitBag',
            ],
        ];
    }

    public function appendVersionConstraints(array $payload): array
    {
        $payload['applicationInfo'] = $this->resolveApplicationInfo();

        return $payload;
    }
}
