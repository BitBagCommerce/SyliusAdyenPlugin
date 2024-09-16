# ![Logo](doc/AdyenPlugin.png)

# Adyen Payments Plugin for Sylius
----

[![](https://img.shields.io/packagist/l/bitbag/adyen-plugin.svg) ](https://packagist.org/packages/bitbag/adyen-plugin "License") [![Slack](https://img.shields.io/badge/community%20chat-slack-FF1493.svg)](http://sylius-devs.slack.com) [![Support](https://img.shields.io/badge/support-contact%20author-blue])](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen)

We want to impact many unique eCommerce projects and build our brand recognition worldwide, so we are heavily involved in creating open-source solutions, especially for Sylius. We have already created over 35 extensions, which have been downloaded almost 2 million times.

You can find more information about our eCommerce services and technologies on our website: https://bitbag.io/. We have also created a unique service dedicated to creating plugins: https://bitbag.io/services/sylius-plugin-development. 

Do you like our work? Would you like to join us? Check out the “Career” tab: https://bitbag.io/pl/kariera. 

# About us
---

BitBag is a software house that implements tailor-made eCommerce platforms with the entire infrastructure—from creating eCommerce platforms to implementing PIM and CMS systems to developing custom eCommerce applications, specialist B2B solutions, and migrations from other platforms.

We actively participate in Sylius's development. We have already completed over 150 projects, cooperating with clients from all over the world, including smaller enterprises and large international companies. We have completed projects for such important brands as **Mytheresa, Foodspring, Planeta Huerto (Carrefour Group), Albeco, Mollie, and ArtNight.**

We have a 70-person team of experts: business analysts and eCommerce consultants, developers, project managers, and QA testers.

**Our services:**
* B2B and B2C eCommerce platform implementations
* Multi-vendor marketplace platform implementations
* eCommerce migrations
* Sylius plugin development
* Sylius consulting
* Project maintenance and long-term support
* PIM and CMS implementations

**Some numbers from BitBag regarding Sylius:**
* 70 experts on board 
* +150 projects delivered on top of Sylius,
* 30 countries of BitBag’s customers,
* 7 years in the Sylius ecosystem.
* +35 plugins created for Sylius

***

 [![](https://bitbag.io/wp-content/uploads/2024/09/badges-sylius.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen)
 
***




## Table of Content

***

* [Overview](#overview)
* [Features](#features)
* [Installation](#installation)
    * [Requirements](#requirements)
    * [Customization](#customization)
    * [Configuration](#configuration)
    * [Security](#security)  
    * [Testing](#testing)
    * [Frontend part](#frontend-part)
* [Additional Sylius resources for developers](#additional-resources-for-developers)
* [License](#license)
* [Contact and Support](#contact-and-support)
* [Community](#community)

# Overview
----
Elevate your Sylius store's payment processing capabilities with the Adyen Plugin. Developed in collaboration with the Adyen Team, this plugin seamlessly integrates Sylius with Adyen, a globally recognized payment processing company. By enabling a wide range of payment methods, this plugin offers a comprehensive solution for your payment gateway needs. It supports all methods available to drop-in.

![Screenshot showing payment methods show in shop](doc/choose-payment.png)

![Screenshot showing payment method config in admin](doc/payment-method-form.png)

Adyen is a growing payment processing company. This plug-in is an integration with Sylius, it was developed with Adyen Team cooperation to provide the best experience.
It supports all methods available to [drop-in](https://docs.adyen.com/online-payments/drop-in-web).

# Features

|**Feature Table** | **Support** |
| -------------    | ----------- |
| **Configuration panel** |
| Encrypted authorization | Yes |
| Encrypted notification password | Yes |        
| Encrypted HMAC key | Yes |
| Credential validation | Yes |
| Live/Sandbox environment | Yes |
| Live endpoint URL prefix | Yes |
| **Payments** |
| [Payment dropin](https://docs.adyen.com/online-payments/web-drop-in) | Yes |
| [Card payments](https://docs.adyen.com/payment-methods/cards) | Yes |
| [Bizum](https://docs.adyen.com/payment-methods/bizum) (Spain only) | Yes |
| [3D Secure](https://docs.adyen.com/online-payments/3d-secure) | Yes |
| **Wallet payments** |
| [WeChat Pay](https://docs.adyen.com/payment-methods/wechat-pay) | Yes |
| [Apple Pay](https://docs.adyen.com/payment-methods/apple-pay) | Yes |
| [Google Pay](https://docs.adyen.com/payment-methods/google-pay) | Yes |
| [AliPay](https://docs.adyen.com/payment-methods/alipay) | Yes |
| **[One-click payment methods](https://docs.adyen.com/online-payments/classic-integrations/api-integration-ecommerce/recurring-payments/authorise-a-recurring-payment#one-click-payments)** |
| [Klarna](https://docs.adyen.com/payment-methods/klarna) | Yes |
| [Dotpay](https://docs.adyen.com/payment-methods/dotpay#page-introduction) | Yes |
| [Twint](https://docs.adyen.com/payment-methods/twint#page-introduction) | Yes |
| [Blik](https://docs.adyen.com/payment-methods/blik#page-introduction) | Yes |
| [PayPal](https://docs.adyen.com/payment-methods/paypal) | Yes |
| [iDeal](https://docs.adyen.com/payment-methods/ideal) | Yes |
| SEPA | Yes |
| [Sofort](https://docs.adyen.com/payment-methods/sofort#page-introduction) | Yes |
| [Bancontact Card](https://docs.adyen.com/payment-methods/bancontact) | Yes |
| **Order management** |
| [Capture](https://docs.adyen.com/issuing/payment-stages#captures) | Yes |
| [Partial refunds](https://docs.adyen.com/issuing/payment-stages#refunds) | Yes |


# Installation

For the full installation guide please go to [here](doc/installation.md).

----

### Requirements

We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.

| Package                | Version        |
|------------------------|----------------|
| PHP                    | ^8.0           |
| ext-json:              | *              |
| sylius/refund-plugin   | ^1.0.0         |
| sylius/resource-bundle | ^1.8           |
| sylius/sylius          | ~1.12 or ~1.13 |
| symfony/messenger      | ^5.4 or ^6.0   |
| adyen/php-api-library  | ^11.0          |

----

### Full installation guide
- [See the full installation guide](doc/installation.md)

## Customization
----
##### You can [decorate](https://symfony.com/doc/current/service_container/service_decoration.html) available services and [extend](https://symfony.com/doc/current/form/create_form_type_extension.html) current forms.

Run the below command to see what Symfony services are shared with this plugin:

```
$ bin/console debug:container bitbag_sylius_adyen_plugin
```

Plug-in heavily relies on Symfony's [Messenger](https://symfony.com/doc/current/messenger.html) Component. All the payment notifications handling actions are done by messages and their handlers. Feel free to play with, decorate or provide a middleware to customize plug-in according to your needs.

All the processing is done using `sylius.command_bus` (`sylius_default.bus` in previous versions). `sylius.event_bus` (`sylius_event.bus`) is used to hook up Refund Plug-in requests and let the Adyen know that refund is requested.

## Configuration
----
The plug-in provides a configuration that can be overrided:

```yaml
bitbag_sylius_adyen:
  logger: ~
  supported_types: ~
```

| property | type | description
| --- | --- | --- |
| logger | null\|string | specifies a logger service name which handles dumping of all traffic between your Sylius instance and Adyen API; useful for debugging. Empty value = disable logging |
| supported_types | null\|array | whitelist of visible payment methods; null = all tested payment methods, array = list of payment types, empty array = don't filter at all and show everything returned by Adyen |

## Security
----

If you find anything that could be a security problem, please reach us first on `hello@bitbag.io` in order to prepare a patch before disclosure.

We know that your money is valuable, so we designed this plug-in to change the payment statuses only at the request of Adyen systems that are signed using HMAC signature.

## Frontend part
----
### Starting and building assets

* Go to `./tests/Application/` directory
* `bin/console assets:install`

### CSS & JS files directory

* CSS: go to `./src/Resources/public/css/**/`
* JS: go to `./src/Resources/public/js/**/`

## Testing
----
```
$ composer install
$ cd tests/Application
$ bin/console assets:install -e test
$ bin/console doctrine:database:create -e test
$ bin/console doctrine:schema:create -e test
$ yarn install
$ yarn encore dev
$ yarn gulp
$ bin/console server:run 127.0.0.1:8080 -e test
$ bin/phpunit
$ bin/behat
```



---

If you need some help with Sylius development, don't be hesitated to contact us directly. You can fill the form on [this site](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen) or send us an e-mail to hello@bitbag.io!

---

## Additional resources for developers
---
To learn more about our contribution workflow and more, we encourage ypu to use the following resources:
* [Sylius Documentation](https://docs.sylius.com/en/latest/)
* [Sylius Contribution Guide](https://docs.sylius.com/en/latest/contributing/)
* [Sylius Online Course](https://sylius.com/online-course/)
* [Sylius Adyen Plugin Blog](https://bitbag.io/blog/adyen-payments-for-sylius)

## License
---

This plugin's source code is completely free and released under the terms of the MIT license.

[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format them nicely because they shouldn't be seen.)

## Contact and Support
---
If you find anything that could be a security problem, please reach us first on hello@bitbag.io in order to prepare a patch before disclosure.

We know that your money is valuable, so we designed this plug-in to change the payment statuses only at the request of Adyen systems that are signed using the HMAC signature.

## Community
----
For online communication, we invite you to chat with us & other users on [Sylius Slack](https://sylius-devs.slack.com/).


[![](https://bitbag.io/wp-content/uploads/2024/09/badges-partners.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen)
