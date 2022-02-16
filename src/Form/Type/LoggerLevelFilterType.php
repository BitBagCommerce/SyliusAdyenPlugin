<?php declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Form\Type;

use Monolog\Logger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class LoggerLevelFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('loggerLevel', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'bitbag_sylius_adyen_plugin.ui.logging.info' => Logger::INFO,
                    'bitbag_sylius_adyen_plugin.ui.logging.debug' => Logger::DEBUG,
                    'bitbag_sylius_adyen_plugin.ui.logging.error' => Logger::ERROR,
                ],
            ]);
    }
}
