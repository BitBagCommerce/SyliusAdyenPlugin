<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Form\Extension;

use BitBag\SyliusAdyenPlugin\Form\Type\ConfigurationType;
use BitBag\SyliusAdyenPlugin\Validator\Constraint\AdyenCredentials;
use Sylius\Bundle\PayumBundle\Form\Type\GatewayConfigType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class GatewayConfigTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$builder->add('config', ConfigurationType::class, [
            'constraints'=>[
                new AdyenCredentials([
                    'groups'=>['sylius']
                ])
            ]
        ]);*/
    }

    public function getExtendedTypes()
    {
        return [GatewayConfigType::class];
    }
}
