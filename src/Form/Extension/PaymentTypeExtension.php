<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Form\Extension;

use BitBag\SyliusAdyenPlugin\Adapter\PaymentMethodsToChoiceAdapter;
use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use BitBag\SyliusAdyenPlugin\Form\Type\PaymentMethodChoiceType;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProvider;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Order\PaymentCheckoutOrderResolverInterface;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\PaymentType;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class PaymentTypeExtension extends AbstractTypeExtension
{
    private $paymentMethodsForCode = [];

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
        $adyen = $builder->create('channels', FormType::class, [
            'compound'=>true,
            'mapped'=>false
        ]);

        $paymentMethods = $this->paymentMethodRepository->findAllByChannel($this->channelContext->getChannel());
        foreach ($paymentMethods as $paymentMethod) {
            $client = $this->adyenClientProvider->getForPaymentMethod($paymentMethod);
            $choices = $this->getChoicesForPaymentMethod($client, $paymentMethod->getCode());
            $adyen->add($paymentMethod->getCode(), PaymentMethodChoiceType::class, [
                'choices'=>$choices,
                'environment'=>$client->getEnvironment()
            ]);
        }

        $builder->add($adyen);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['payment_methods'] = $this->paymentMethodsForCode;
    }


    private function getChoicesForPaymentMethod(
        AdyenClientInterface $client,
        string $code
    ): array {
        $order = $this->paymentCheckoutOrderResolver->resolve();

        $result = $client->getAvailablePaymentMethods(
            $order->getLocaleCode(),
            $order->getBillingAddress()->getCountryCode(),
            $order->getTotal(),
            $order->getCurrencyCode()
        );

        $this->paymentMethodsForCode[$code] = $result;

        return (new PaymentMethodsToChoiceAdapter())($result);
    }

    public function getExtendedTypes()
    {
        return [PaymentType::class];
    }
}
