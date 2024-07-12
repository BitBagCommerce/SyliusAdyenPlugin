<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

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
        $resolver->setDefault('payment_methods', []);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function buildView(
        FormView $view,
        FormInterface $form,
        array $options,
    ): void {
        parent::buildView($view, $form, $options);
        $view->vars['environment'] = $options['environment'];
        $view->vars['payment_methods'] = $options['payment_methods'];
    }
}
