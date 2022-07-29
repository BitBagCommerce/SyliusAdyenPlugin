<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Callback;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PreserveOrderTokenUponRedirectionCallback
{
    public const NON_FINALIZED_CART_SESSION_KEY = '_ADYEN_PAYMENT_IN_PROGRESS';

    /** @var SessionInterface */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function __invoke(OrderInterface $order): void
    {
        $tokenValue = $order->getTokenValue();

        if (null === $tokenValue) {
            return;
        }

        $this->session->set(
            self::NON_FINALIZED_CART_SESSION_KEY,
            $tokenValue
        );
    }
}
