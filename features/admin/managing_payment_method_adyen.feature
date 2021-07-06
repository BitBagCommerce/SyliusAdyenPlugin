@managing_payment_method_adyen
Feature: Adding a new payment method
  In order to pay for orders in different ways
  As an Administrator
  I want to add a new payment method to the registry

  Background:
    Given the store operates on a single channel in "United States"
    And I am logged in as an administrator

  @ui
  Scenario: Adding a new adyen payment method with passing validation
    Given I want to create a new Adyen payment method
    And Adyen service will confirm merchantAccount "mer" and apiKey "api" are valid
    When I name it "Adyen" in "English (United States)"
    And I specify its code as "adyen_test"
    And I specify test configuration with merchantAccount "mer" and apiKey "api"
    And I add it
    Then I should be notified that it has been successfully created
    And the payment method "Adyen" should appear in the registry