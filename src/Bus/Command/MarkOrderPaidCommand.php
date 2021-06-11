<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bus\Command;

use BitBag\SyliusAdyenPlugin\Bus\CommandInterface;
use Sylius\Component\Core\Model\OrderInterface;

class MarkOrderPaidCommand implements CommandInterface
{
    /** @var OrderInterface */
    private $order;
    /**
     * @var array|null
     */
    private $response;

    public function __construct(OrderInterface $order, ?array $response = null)
    {
        $this->order = $order;
        $this->response = $response;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    public static function createForOrder(OrderInterface $order, ?array $response = null)
    {
        return new static($order, $response);
    }

    /**
     * @return array|null
     */
    public function getResponse(): ?array
    {
        return $this->response;
    }

}
