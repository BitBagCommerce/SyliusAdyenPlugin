<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Processor\PaymentResponseProcessor;

use Sylius\Component\Core\Model\PaymentInterface;

abstract class AbstractProcessor implements Processor
{
    protected function isResultCodeSupportedForPayment(?PaymentInterface $payment, array $resultCodes): bool
    {
        if ($payment === null) {
            return false;
        }

        $details = $payment->getDetails();
        if (!isset($details['resultCode'])) {
            return false;
        }

        return in_array(
            strtolower((string) $details['resultCode']),
            $resultCodes,
            true
        );
    }
}
