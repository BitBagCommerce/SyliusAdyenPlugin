## Installation

1. Require with composer

```bash
composer require bitbag/adyen-plugin
```
2. Add plugin dependencies to your `config/bundles.php` file:

```php
return [
    ...
    BitBag\SyliusAdyenPlugin\BitBagSyliusAdyenPlugin::class => ['all' => true],
];
```

3. Import required config in your `config/packages/_sylius.yaml` file:

```yaml
# config/packages/_sylius.yaml

imports:
    ...
    - { resource: "@BitBagSyliusAdyenPlugin/Resources/config/config.yaml" }
```

4. Import the routing in your `config/routes.yaml` file:

```yaml
# config/routes.yaml

bitbag_sylius_adyen_plugin:
    resource: "@BitBagSyliusAdyenPlugin/Resources/config/shop_routing.yaml"
```

5. Add Adyen payment method as supported refund gateway in `config/packages/_sylius.yaml`

```yaml
# config/packages/_sylius.yaml

   parameters:
      sylius_refund.supported_gateways:
         - offline
         - adyen
``` 

6. Copy Sylius templates overridden by plug-in to your templates directory (`templates/bundles/`):

```
mkdir -p templates/bundles/SyliusAdminBundle/
mkdir -p templates/bundles/SyliusShopBundle/

cp -R vendor/bitbag/sylius-adyen-plugin/tests/Application/templates/bundles/SyliusAdminBundle/* templates/bundles/SyliusAdminBundle/
cp -R vendor/bitbag/sylius-adyen-plugin/tests/Application/templates/bundles/SyliusShopBundle/* templates/bundles/SyliusShopBundle/
```

7. Complete [refund plug-in](https://github.com/Sylius/RefundPlugin) install steps (e.g. templates and so on)

8. Install assets

```
bin/console assets:install
```

**Note:** If you are running it on production, add the `-e prod` flag to this command.

9. Synchronize the database

```
bin/console doctrine:schema:update
```

10. [Obtain Adyen credentials and configure payment method](configuration.md)

11. Add logging to your environment in {dev, prod, staging}/monolog.yaml

```yaml
monolog:
    channels: [adyen]
    handlers: # Add alongside other handlers you might have
        doctrine:
            type: service
            channels: [adyen]
            id: bitbag.sylius_adyen_plugin.logging.monolog.doctrine_handler
```
