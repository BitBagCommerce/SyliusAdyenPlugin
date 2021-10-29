<?php
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Normalizer;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ShippingLineGenerator implements ShippingLineGeneratorInterface
{
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function generate(array $items, OrderInterface $order): array
    {
        $netSum = array_sum(array_column($items, 'amountExcludingTax'));
        $totalSum = array_sum(array_column($items, 'amountIncludingTax'));

        return [
            'amountExcludingTax' => $order->getTotal() - $order->getTaxTotal() - $netSum,
            'amountIncludingTax' => $order->getTotal() - $totalSum,
            'id' => $this->translator->trans('sylius.ui.shipping'),
            'quantity' => 1,
        ];
    }
}
