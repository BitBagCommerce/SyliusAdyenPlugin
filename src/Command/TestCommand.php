<?php


namespace BitBag\SyliusAdyenPlugin\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends \Symfony\Component\Console\Command\Command
{

    protected static $defaultName = 'app:test';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new \Adyen\Client();

        $client->setXApiKey("AQEkhmfuXNWTK0Qc+iSSm3AapuPCENmSp+0jS5/eTOkvRWd50GqdEMFdWw2+5HzctViMSCJMYAc=-I/vPUkVOC0sraNXTF0j9OSgprjxrK2Ck7e7IoOkKids=-S7BUq^y)UP9ry]mq");
        $client->setEnvironment(\Adyen\Environment::TEST);
        $client->setTimeout(30);

        $service = new \Adyen\Service\Checkout($client);

        $json = '{
      "amount": {
        "value": 1500,
        "currency": "EUR"
      },
      "reference": "payment-test",
      "merchantAccount": "BitBag982ECOM",
      "countryCode": "PL",
      "shopperLocale": "pl_PL"
}';

        $params = json_decode($json, true);

        $result = $service->paymentMethods($params);

        /*$payload = '{
  "amount": {
    "currency": "USD",
    "value": 1000
  },
  "reference": "Your order number",
  "paymentMethod": {
    "type": "scheme",
    "number": "4111111111111111",
    "expiryMonth": "03",
    "expiryYear": "2030",
    "holderName": "John Smith",
    "cvc": "737"
  },
  "returnUrl": "https://example.org/",
  "merchantAccount": "BitBag982ECOM"
}';

        $payload = json_decode($payload, true);
        $result = $service->payments($payload);*/

        dump($result);

        return 0;
    }


}