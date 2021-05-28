<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Form\Extension;

use BitBag\SyliusAdyenPlugin\AdyenGatewayFactory;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodChoiceType;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class PaymentMethodChoiceTypeExtension extends \Symfony\Component\Form\AbstractTypeExtension
{
    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    public function __construct(AdyenClientProvider $adyenClientProvider)
    {
        $this->adyenClientProvider = $adyenClientProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $order = $this->getOrder($form);

        $view->vars['adyen'] = [];

        foreach ($view->vars['choices'] as $choice) {
            /**
             * @var $paymentMethod \Sylius\Component\Core\Model\PaymentMethod
             */
            $paymentMethod = $choice->data;
            if (
                $paymentMethod->getGatewayConfig()
                && $paymentMethod->getGatewayConfig()->getGatewayName() == AdyenGatewayFactory::FACTORY_NAME
            ) {
                $view->vars['adyen'][$paymentMethod->getCode()] = $this->getPaymentMethodsForCode(
                    $order,
                    $paymentMethod->getCode()
                );
            }
        }
    }

    private function getPaymentMethodsForCode(OrderInterface $order, string $code): array
    {
        $client = $this->adyenClientProvider->getClientForCode($code);

        return $client->getAvailablePaymentMethods(
            $order->getLocaleCode(),
            $order->getBillingAddress()->getCountryCode(),
            $order->getTotal(),
            $order->getCurrencyCode()
        );
    }

    private function getOrder(FormInterface $form): ?OrderInterface
    {
        $parent = $form;

        do {
            $parent = $parent->getParent();
            $data = $parent->getData();
        } while ($parent !== null && !$data instanceof OrderInterface);

        return $data;
    }

    public function getExtendedTypes()
    {
        return [PaymentMethodChoiceType::class];
    }
}
