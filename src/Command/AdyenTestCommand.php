<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Command;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AdyenTestCommand extends Command
{
    protected static $defaultName = 'adyen:test';

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    public function __construct(AdyenClientProvider $adyenClientProvider)
    {
        parent::__construct();
        $this->adyenClientProvider = $adyenClientProvider;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->adyenClientProvider->getClientForCode('adyen1');
        var_dump($client->requestRefund('853626183548082J', 1234, 'EUR', 'blahhhh'));
    }
}
