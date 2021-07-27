<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Resolver\Version;

use PackageVersions\FallbackVersions;
use Sylius\Bundle\CoreBundle\Application\Kernel;

class VersionResolver
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
                return \Composer\InstalledVersions::getPrettyVersion(self::PACKAGE_NAME);
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
        return [
            'merchantApplication' => [
                'name' => 'adyen-sylius',
                'version' => $this->getPluginVersion()
            ],
            'externalPlatform' => [
                'name' => 'Sylius',
                'version' => Kernel::VERSION,
                'integrator' => 'BitBag'
            ]
        ];
    }

    public function appendVersionConstraints(array $payload): array
    {
        $payload['applicationInfo'] = $this->resolveApplicationInfo();

        return $payload;
    }
}
