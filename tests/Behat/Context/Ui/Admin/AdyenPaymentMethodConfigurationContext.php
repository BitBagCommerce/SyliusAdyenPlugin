<?php

declare(strict_types=1);
/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

namespace Tests\BitBag\SyliusAdyenPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use BitBag\SyliusAdyenPlugin\Form\Type\CredentialType;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Tests\BitBag\SyliusAdyenPlugin\Behat\Page\Admin\PaymentMethod\UpdatePageInterface;
use Webmozart\Assert\Assert;

class AdyenPaymentMethodConfigurationContext implements Context
{
    /** @var UpdatePageInterface */
    private $updatePage;

    public function __construct(
        UpdatePageInterface $updatePage
    ) {
        $this->updatePage = $updatePage;
    }

    /**
     * @Then I want fields :fieldNames to be filled as placeholder
     */
    public function iWantAFieldToBeFilledAsPlaceholder(string $fieldNames): void
    {
        $fieldNames = explode(',', $fieldNames);
        foreach ($fieldNames as $fieldName) {
            $fieldName = trim($fieldName);
            Assert::eq($this->updatePage->getElementValue($fieldName), CredentialType::CREDENTIAL_PLACEHOLDER);
        }
    }

    /**
     * @Then I want the payment method :paymentMethod configuration to be:
     *
     * @param \Sylius\Component\Core\Model\PaymentMethodInterface $paymentMethod
     */
    public function iWantThePaymentMethodConfigurationToBe(TableNode $table, PaymentMethodInterface $paymentMethod)
    {
        foreach ($table->getHash() as $row) {
            Assert::eq($paymentMethod->getGatewayConfig()->getConfig()[$row['name']], $row['value']);
        }
    }
}
