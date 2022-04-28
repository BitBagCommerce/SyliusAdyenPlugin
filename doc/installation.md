## Installation

1. Complete [refund plug-in](https://github.com/Sylius/RefundPlugin) installation steps

2. Require with composer

```bash
composer require bitbag/sylius-adyen-plugin --no-scripts
```
3. When using Symfony flex the proper bundle class will be automatically registered in your bundles.php file. Otherwise, add it to your `config/bundles.php` file:

```php
return [
    // ...
    BitBag\SyliusAdyenPlugin\BitBagSyliusAdyenPlugin::class => ['all' => true],
];
```

4. Import required config in your `config/packages/_sylius.yaml` file:

```yaml
# config/packages/_sylius.yaml

imports:
    ...
    - { resource: "@BitBagSyliusAdyenPlugin/Resources/config/config.yaml" }
```

5. Import the routing in your `config/routes.yaml` file:

```yaml
# config/routes.yaml

bitbag_sylius_adyen_plugin:
    resource: "@BitBagSyliusAdyenPlugin/Resources/config/routing.yaml"
```

6. Add logging to your environment in {dev, prod, staging}/monolog.yaml

```yaml
monolog:
    channels: [adyen]
    handlers: # Add alongside other handlers you might have
        doctrine:
            type: service
            channels: [adyen]
            id: bitbag.sylius_adyen_plugin.logging.monolog.doctrine_handler
```

7. Add Adyen payment method as a supported refund gateway in `config/packages/_sylius.yaml`

```yaml
# config/packages/_sylius.yaml

   parameters:
      sylius_refund.supported_gateways:
         - offline
         - adyen
```

8. Copy Sylius templates overridden by plug-in to your templates directory (`templates/bundles/`):

```
mkdir -p templates/bundles/SyliusAdminBundle/
mkdir -p templates/bundles/SyliusShopBundle/

cp -R vendor/bitbag/sylius-adyen-plugin/tests/Application/templates/bundles/SyliusAdminBundle/* templates/bundles/SyliusAdminBundle/
cp -R vendor/bitbag/sylius-adyen-plugin/tests/Application/templates/bundles/SyliusShopBundle/* templates/bundles/SyliusShopBundle/
```

9. Execute migrations

```
bin/console doctrine:migrations:migrate
```

10. Install assets

```
bin/console assets:install
```

11. Clear cache

```
bin/console cache:clear
```

**Note:** If you are running it on production, add the `-e prod` flag to this command.

12. [Obtain Adyen credentials and configure the payment method](configuration.md)


13. If you want to access the log page, visit /adyen/log.
