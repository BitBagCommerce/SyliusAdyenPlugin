# Installation

## Overview:
GENERAL
- [Requirements](#requirements)
- [Composer](#composer)
- [Basic configuration](#basic-configuration)
---
FRONTEND
- [Templates](#templates)
- [Webpack](#webpack)
---
ADDITIONAL
- [Additional configuration](#additional-configuration)
- [Known Issues](#known-issues)
---

## Requirements:
### Installed BitBagRefundPlugin
Complete installation instructions for the BitBagRefundPlugin can be found here:

- [BitBagRefundPlugin installation](https://github.com/Sylius/RefundPlugin)

We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.

| Package       | Version         |
|---------------|-----------------|
| PHP           | \>8.0           |
| sylius/sylius | 1.12.x - 1.13.x |
| MySQL         | \>= 5.7         |
| NodeJS        | \>= 18.x        |

## Composer:
```bash
composer require bitbag/sylius-adyen-plugin --no-scripts
```

## Basic configuration:
Add plugin dependencies to your `config/bundles.php` file:

```php
# config/bundles.php

return [
    ...
    BitBag\SyliusAdyenPlugin\BitBagSyliusAdyenPlugin::class => ['all' => true],
];
```

Import required config in your `config/packages/_sylius.yaml` file:

```yaml
# config/packages/_sylius.yaml

imports:
    ...
    - { resource: "@BitBagSyliusAdyenPlugin/Resources/config/config.yaml" }
```

Add Adyen payment method as a supported refund gateway in `config/packages/_sylius.yaml`:
```yaml
# config/packages/_sylius.yaml

parameters:
  sylius_refund.supported_gateways:
     - offline
     - adyen
```

Import routing in your `config/routes.yaml` file:
```yaml
# config/routes.yaml

bitbag_sylius_adyen_plugin:
    resource: "@BitBagSyliusAdyenPlugin/Resources/config/routing.yaml"
```

Add logging to your environment in config/packages/{dev, prod, staging}/monolog.yaml
```yaml
# config/packages/{dev, prod, staging}/monolog.yaml

monolog:
    channels: [adyen]
    handlers: # Add alongside other handlers you might have
        doctrine:
            type: service
            channels: [adyen]
            id: bitbag.sylius_adyen_plugin.logging.monolog.doctrine_handler
```

### Update your database
First, please run legacy-versioned migrations by using command:
```bash
bin/console doctrine:migrations:migrate
```

After migration, please create a new diff migration and update database:
```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```
### Clear application cache by using command:
```bash
bin/console cache:clear
```
**Note:** If you are running it on production, add the `-e prod` flag to this command.

## Templates
Copy required templates into correct directories in your project.

**AdminBundle** (`templates/bundles/SyliusAdminBundle`):
```
vendor/bitbag/sylius-adyen-plugin/tests/Application/templates/bundles/SyliusAdminBundle/Order/Show/_payment.html.twig
vendor/bitbag/sylius-adyen-plugin/tests/Application/templates/bundles/SyliusAdminBundle/Order/Show/_payments.html.twig
```

**ShopBundle** (`templates/bundles/SyliusShopBundle`):
```
vendor/bitbag/sylius-adyen-plugin/tests/Application/templates/bundles/SyliusShopBundle/Checkout/Complete/_navigation.html.twig
vendor/bitbag/sylius-adyen-plugin/tests/Application/templates/bundles/SyliusShopBundle/Checkout/SelectPayment/_payment.html.twig
```

Install assets:
```bash
bin/console assets:install
```

## Webpack
### Run commands
```bash
yarn install
yarn encore dev # or prod, depends on your environment
```

## Additional configuration
- [Obtain Adyen credentials and configure the payment method](https://github.com/BitBagCommerce/SyliusAdyenPlugin/blob/master/doc/configuration.md)

If you want to access the log page, visit /adyen/log.

## Known issues
### Translations not displaying correctly
For incorrectly displayed translations, execute the command:
```bash
bin/console cache:clear
```
