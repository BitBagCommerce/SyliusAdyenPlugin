## Installation

The Refund plugin does not yet have stable release.
You can install first Refund plugin by adding this line to composer.json

```diff
    "require": {
        "sylius/refund-plugin": "1.0.0-RC10 as 1.0.0",
    },
    ...
```
Or configure project to accept releases candidate version

```bash
composer config minimum-stability rc
composer config prefer-stable true
```

See: [refund plug-in README](https://github.com/Sylius/RefundPlugin)

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

5. Add image dir parameter in `config/packages/_sylius.yaml`

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

cp -R vendor/bitbag/adyen-plugin/tests/Application/templates/bundles/SyliusAdminBundle/* templates/bundles/SyliusAdminBundle/
cp -R vendor/bitbag/adyen-plugin/tests/Application/templates/bundles/SyliusShopBundle/* templates/bundles/SyliusShopBundle/
```

7. Install assets

```
bin/console assets:install
```

**Note:** If you are running it on production, add the `-e prod` flag to this command.

8. Synchronize the database

```
bin/console doctrine:schema:update
```

9. [Obtain Adyen credentials and configure payment method](configuration.md)