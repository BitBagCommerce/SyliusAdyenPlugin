# ![Logo](doc/AdyenPlugin.png)

# Adyen Payments Plugin for Sylius
----

[![](https://img.shields.io/packagist/l/bitbag/adyen-plugin.svg) ](https://packagist.org/packages/bitbag/adyen-plugin "License") [ ![](https://img.shields.io/packagist/v/bitbag/mollie-plugin.svg) ](https://packagist.org/packages/bitbag/mollie-plugin "Version") [ ![](https://img.shields.io/scrutinizer/g/BitBagCommerce/SyliusMolliePlugin.svg) ](https://scrutinizer-ci.com/g/BitBagCommerce/SyliusMolliePlugin/ "Scrutinizer") [![](https://poser.pugx.org/bitbag/mollie-plugin/downloads)](https://packagist.org/packages/bitbag/mollie-plugin "Total Downloads") [![Slack](https://img.shields.io/badge/community%20chat-slack-FF1493.svg)](http://sylius-devs.slack.com) [![Support](https://img.shields.io/badge/support-contact%20author-blue])](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen)

At BitBag we do believe in open source. However, we are able to do it just because of our awesome clients, who are kind enough to share some parts of our work with the community. Therefore, if you feel like there is a possibility for us working together, feel free to reach us out. You will find out more about our professional services, technologies and contact details at [https://bitbag.io/](https://bitbag.io/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen).

## Table of Content

***

* [Overview](#overview)
* [Support](#we-are-here-to-help)
* [Installation](#installation)
    * [Requirements](#requirements)
    * [Usage](#usage)
    * [Customization](#customization)
    * [Testing](#testing)
    * [Frontend part](#frontend-part)
* [About us](#about-us)
    * [Community](#community)
* [Demo Sylius shop](#demo-sylius-shop)
* [Additional Sylius resources for developers](#additional-resources-for-developers)
* [License](#license)
* [Contact](#contact)

# Overview
----

![Screenshot showing payment methods show in shop](doc/choose-payment.png)

![Screenshot showing payment method config in admin](doc/payment-method-form.png)

Adyen is a growing payment processing company. This plug-in is an integration with Sylius, it was developed with Adyen Team cooperation to provide the best experience.
It supports all methods available to [drop-in](https://docs.adyen.com/online-payments/drop-in-web). Available methods are depended on your contract with Adyen, it includes: 

1. Credit Cards (Master Card, VISA, American Express)
2. PayPal
5. iDEAL
6. SEPA
7. SOFORT

## We are here to help
This **open-source plugin was developed to help the Sylius community** and make Adyen payments platform available to any Sylius store. If you have any additional questions, would like help with installing or configuring the plugin or need any assistance with your Sylius project - let us know!

[![](https://bitbag.io/wp-content/uploads/2020/10/button-contact.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen)


# Installation
----

### Requirements

We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.

| Package | Version |
| --- | --- |
| PHP |  ^7.3 |
| ext-json:  | * |
| sylius/refund-plugin |  ^1.0.0-RC.10 |
| sylius/sylius |  ^1.9.0 |
| symfony/messenger |   ^4.4 |
| adyen/php-api-library | ^10.1 |

----

For the full installation guide please go to [installation](doc/installation.md)

## Customization
----
##### You can [decorate](https://symfony.com/doc/current/service_container/service_decoration.html) available services and [extend](https://symfony.com/doc/current/form/create_form_type_extension.html) current forms.

Run the below command to see what Symfony services are shared with this plugin:

```
$ bin/console debug:container bitbag_sylius_adyen_plugin
```
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
$ bin/console server:run 127.0.0.1:8080 -e test
$ bin/phpunit
$ bin/behat
```

# About us
---

BitBag is an agency that provides high-quality **eCommerce and Digital Experience software**. Our main area of expertise includes eCommerce consulting and development for B2C, B2B, and Multi-vendor Marketplaces.
The scope of our services related to Sylius includes:
- **Consulting** in the field of strategy development
- Personalized **headless software development**
- **System maintenance and long-term support**
- **Outsourcing**
- **Plugin development**
- **Data migration**

Some numbers regarding Sylius:
* **20+ experts** including consultants, UI/UX designers, Sylius trained front-end and back-end developers,
* **100+ projects** delivered on top of Sylius,
* Clients from  **20+ countries**
* **3+ years** in the Sylius ecosystem.

---

If you need some help with Sylius development, don't be hesitated to contact us directly. You can fill the form on [this site](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen) or send us an e-mail to hello@bitbag.io!

---

[![](https://bitbag.io/wp-content/uploads/2020/10/badges-sylius.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen)

## Community
----
For online communication, we invite you to chat with us & other users on [Sylius Slack](https://sylius-devs.slack.com/).

# Demo Sylius shop
---

@todo: still relevant?

We created a demo app with some useful use-cases of plugins!
Visit b2b.bitbag.shop to take a look at it. The admin can be accessed under b2b.bitbag.shop/admin/login link and sylius: sylius credentials.
Plugins that we have used in the demo:
| BitBag's Plugin | GitHub | Sylius' Store|
| ------ | ------ | ------|
| ACL PLugin | *Private. Available after the purchasing.*| https://plugins.sylius.com/plugin/access-control-layer-plugin/|
| Braintree Plugin | https://github.com/BitBagCommerce/SyliusBraintreePlugin |https://plugins.sylius.com/plugin/braintree-plugin/|
| CMS Plugin | https://github.com/BitBagCommerce/SyliusCmsPlugin | https://plugins.sylius.com/plugin/cmsplugin/|
| Elasticsearch Plugin | https://github.com/BitBagCommerce/SyliusElasticsearchPlugin | https://plugins.sylius.com/plugin/2004/|
| Mailchimp Plugin | https://github.com/BitBagCommerce/SyliusMailChimpPlugin | https://plugins.sylius.com/plugin/mailchimp/ |
| Multisafepay Plugin | https://github.com/BitBagCommerce/SyliusMultiSafepayPlugin |
| Wishlist Plugin | https://github.com/BitBagCommerce/SyliusWishlistPlugin | https://plugins.sylius.com/plugin/wishlist-plugin/|
| **Sylius' Plugin** | **GitHub** | **Sylius' Store** |
| Admin Order Creation Plugin | https://github.com/Sylius/AdminOrderCreationPlugin | https://plugins.sylius.com/plugin/admin-order-creation-plugin/ |
| Invoicing Plugin | https://github.com/Sylius/InvoicingPlugin | https://plugins.sylius.com/plugin/invoicing-plugin/ |
| Refund Plugin | https://github.com/Sylius/RefundPlugin | https://plugins.sylius.com/plugin/refund-plugin/ |

**If you need an overview of Sylius' capabilities, schedule a consultation with our expert.**

[![](https://bitbag.io/wp-content/uploads/2020/10/button_free_consulatation-1.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen)

## Additional resources for developers
---
To learn more about our contribution workflow and more, we encourage ypu to use the following resources:
* [Sylius Documentation](https://docs.sylius.com/en/latest/)
* [Sylius Contribution Guide](https://docs.sylius.com/en/latest/contributing/)
* [Sylius Online Course](https://sylius.com/online-course/)

## License
---

This plugin's source code is completely free and released under the terms of the MIT license.

[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen.)

## Contact
---
If you want to contact us, the best way is to fill the form on [our website](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen) or send us an e-mail to hello@bitbag.io with your question(s). We guarantee that we answer as soon as we can!

[![](https://bitbag.io/wp-content/uploads/2020/10/footer.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_adyen)
