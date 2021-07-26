<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Form\Type;

use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PaymentMethodChoiceType extends ChoiceType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('environment', AdyenClientInterface::TEST_ENVIRONMENT);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
        $view->vars['environment'] = $options['environment'];
    }
}
