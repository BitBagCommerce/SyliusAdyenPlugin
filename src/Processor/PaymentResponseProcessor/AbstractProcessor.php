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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractProcessor implements ProcessorInterface
{
    public const PAYMENT_STATUS_RECEIVED_CODE = 'payment_status_received';

    public const FLASH_INFO = 'info';

    public const FLASH_ERROR = 'error';

    /** @var TranslatorInterface|null */
    protected $translator;

    protected function isResultCodeSupportedForPayment(?PaymentInterface $payment, array $resultCodes): bool
    {
        if (null === $payment) {
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

    protected function addFlash(
        Request $request,
        string $type,
        string $message
    ): void {
        if (null !== $this->translator) {
            $message = $this->translator->trans($message);
        }


        $session = $request->getSession();
        $session->getBag('flashes')->add($type, $message);
//        $session->getFlashBag()->add($type, $message);
    }
}
