# Increazy checkout Magento 2

Module to add Increazy API in Magento 2.3.X, follow the installation steps:

1. Copy the Increazy folder to app/code.
2. Execute:

```bash
bin/magento module:enable Increazy_Checkout
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f
```