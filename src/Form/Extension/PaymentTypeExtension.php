<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Form\Extension;

use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Order\PaymentCheckoutOrderResolverInterface;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\PaymentType;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

class PaymentTypeExtension extends AbstractTypeExtension
{
    /** @var PaymentCheckoutOrderResolverInterface */
    private $paymentCheckoutOrderResolver;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var AdyenClientProvider */
    private $adyenClientProvider;

    public function __construct(
        PaymentCheckoutOrderResolverInterface $paymentCheckoutOrderResolver,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ChannelContextInterface $channelContext,
        AdyenClientProvider $adyenClientProvider
    ) {
        $this->paymentCheckoutOrderResolver = $paymentCheckoutOrderResolver;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->channelContext = $channelContext;
        $this->adyenClientProvider = $adyenClientProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $adyen = $builder->create('details', FormType::class, [
            'compound'=>true
        ]);

        $paymentMethods = $this->paymentMethodRepository->findAllByChannel($this->channelContext->getChannel());
        foreach ($paymentMethods as $paymentMethod) {
            $choices = $this->getChoicesForPaymentMethod($paymentMethod);
            $adyen->add($paymentMethod->getCode(), ChoiceType::class, [
                'choices'=>$choices
            ]);
        }

        $builder->add($adyen);
    }

    private function getChoicesForPaymentMethod(PaymentMethodInterface $paymentMethod): array
    {
        $order = $this->paymentCheckoutOrderResolver->resolve();
        $client = $this->adyenClientProvider->getForPaymentMethod($paymentMethod);
        $methods = $client->getAvailablePaymentMethods(
            $order->getLocaleCode(),
            $order->getBillingAddress()->getCountryCode(),
            $order->getTotal(),
            $order->getCurrencyCode()
        );

        return array_flip($methods);
    }

    public function getExtendedTypes()
    {
        return [PaymentType::class];
    }
}
