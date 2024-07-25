<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Form\Extension;

use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use BitBag\SyliusAdyenPlugin\Form\Type\PaymentMethodChoiceType;
use BitBag\SyliusAdyenPlugin\Provider\AdyenClientProviderInterface;
use BitBag\SyliusAdyenPlugin\Repository\PaymentMethodRepositoryInterface;
use BitBag\SyliusAdyenPlugin\Resolver\Order\PaymentCheckoutOrderResolverInterface;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\PaymentType;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Webmozart\Assert\Assert;

final class PaymentTypeExtension extends AbstractTypeExtension
{
    /** @var PaymentCheckoutOrderResolverInterface */
    private $paymentCheckoutOrderResolver;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var AdyenClientProviderInterface */
    private $adyenClientProvider;

    public function __construct(
        PaymentCheckoutOrderResolverInterface $paymentCheckoutOrderResolver,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ChannelContextInterface $channelContext,
        AdyenClientProviderInterface $adyenClientProvider,
    ) {
        $this->paymentCheckoutOrderResolver = $paymentCheckoutOrderResolver;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->channelContext = $channelContext;
        $this->adyenClientProvider = $adyenClientProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $adyen = $builder->create('channels', FormType::class, [
            'compound' => true,
            'mapped' => false,
        ]);

        $paymentMethods = $this->paymentMethodRepository->findAllByChannel($this->channelContext->getChannel());
        foreach ($paymentMethods as $paymentMethod) {
            $client = $this->adyenClientProvider->getForPaymentMethod($paymentMethod);
            $paymentMethods = $this->getPaymentMethods($client);
            $adyen->add((string) $paymentMethod->getCode(), PaymentMethodChoiceType::class, [
                'environment' => $client->getEnvironment(),
                'payment_methods' => $paymentMethods,
            ]);
        }

        $builder->add($adyen);
    }

    private function getPaymentMethods(
        AdyenClientInterface $client,
    ): array {
        $order = $this->paymentCheckoutOrderResolver->resolve();

        $result = $client->getAvailablePaymentMethods($order);
        Assert::keyExists($result, 'paymentMethods');

        return (array) $result['paymentMethods'];
    }

    public static function getExtendedTypes(): array
    {
        return [PaymentType::class];
    }
}
