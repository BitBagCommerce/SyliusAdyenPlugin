<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\PaymentType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PaymentTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('details', TextType::class);
    }

    public function getExtendedTypes()
    {
        return [PaymentType::class];
    }
}
