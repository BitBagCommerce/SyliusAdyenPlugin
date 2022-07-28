<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class AbstractPaymentNormalizer implements NormalizerInterface
{
    public const NORMALIZER_ENABLED = 'bitbag_adyen_payment_normalizer';

    public function supportsNormalization(
        $data,
        string $format = null,
        array $context = []
    ): bool {
        return isset($context[self::NORMALIZER_ENABLED]);
    }
}
