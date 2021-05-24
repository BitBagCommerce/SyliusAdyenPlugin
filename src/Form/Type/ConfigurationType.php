<?php

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Form\Type;

use BitBag\SyliusAdyenPlugin\Client\AdyenClientInterface;
use BitBag\SyliusAdyenPlugin\Validator\Constraint\AdyenCredentials;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('environment', ChoiceType::class, [
                'choices' => [
                    'bitbag_sylius_adyen_plugin.ui.test_platform' => AdyenClientInterface::TEST_ENVIRONMENT,
                    'bitbag_sylius_adyen_plugin.ui.live_platform' => AdyenClientInterface::LIVE_ENVIRONMENT,
                ],
                'label' => 'bitbag_sylius_adyen_plugin.ui.platform',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_adyen_plugin.environment.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ])
            ->add('merchantAccount', TextType::class, [
                'label' => 'bitbag_sylius_adyen_plugin.ui.merchant_account',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_adyen_plugin.merchant_account.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ])
            ->add('hmacKey', TextType::class, [
                'label' => 'bitbag_sylius_adyen_plugin.ui.hmac_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_adyen_plugin.hmac_key.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ])
            ->add('hmacNotification', TextType::class, [
                'label' => 'bitbag_sylius_adyen_plugin.ui.hmac_notification',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_adyen_plugin.hmac_notification.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ])
            ->add('skinCode', TextType::class, [
                'label' => 'bitbag_sylius_adyen_plugin.ui.skin_code',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_adyen_plugin.skin_code.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ])
            ->add('wsUser', TextType::class, [
                'label' => 'bitbag_sylius_adyen_plugin.ui.ws_user',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_adyen_plugin.ws_user.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ])
            ->add('wsUserPassword', TextType::class, [
                'label' => 'bitbag_sylius_adyen_plugin.ui.ws_user_password',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_adyen_plugin.ws_user_password.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ])
            ->add('apiKey', TextType::class, [
                'label' => 'apiKey',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_adyen_plugin.ws_user_password.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('constraints', [
            new AdyenCredentials()
        ]);
    }
}
