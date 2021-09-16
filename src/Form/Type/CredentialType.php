<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace BitBag\SyliusAdyenPlugin\Form\Type;

use Symfony\Component\Form\Event\SubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class CredentialType extends PasswordType
{
    public const CREDENTIAL_PLACEHOLDER = '#CREDENTIAL_PLACEHOLDER#';

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (strlen((string) $view->vars['value']) === 0 || $form->isSubmitted()) {
            return;
        }

        $view->vars['value'] = self::CREDENTIAL_PLACEHOLDER;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (SubmitEvent $event): void {
                if ($event->getData() !== self::CREDENTIAL_PLACEHOLDER) {
                    return;
                }

                $event->setData(
                    $event->getForm()->getNormData()
                );
            }
        );
    }
}