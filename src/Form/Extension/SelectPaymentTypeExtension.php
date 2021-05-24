<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectPaymentType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SelectPaymentTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('teeeeeeee', TextType::class, [
            'mapped'=>false
        ]);
    }

    public function getExtendedTypes()
    {
        return [SelectPaymentType::class];
    }
}
