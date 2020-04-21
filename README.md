# Drop_DiscountTax
Adds a configuration on the Magento 2 backoffice that allows you to show the discount field without taxes on the order/invoice/creditmemo and on related PDF documents and sales email.

## Installation
- Install module through composer (recommended):
```sh
$ composer config repositories.drop.discounttax vcs https://github.com/DevelopersDrop/Drop_DiscountTax
$ composer require drop/module-discount-tax
```

- Install module manually:
    - Copy these files in app/code/Drop/Drop_DiscountTax/

- After installing the extension, run the following commands:
```sh
$ php bin/magento module:enable Drop_DiscountTax
$ php bin/magento setup:upgrade
$ php bin/magento setup:di:compile
$ php bin/magento setup:static-content:deploy
$ php bin/magento cache:clear
```

## Requirements
- PHP >= 7.0.0

## Compatibility
- Magento >= 2.2
- Not tested on 2.1 and 2.0

## Support
If you encounter any problems or bugs, please create an issue on [Github](https://github.com/DevelopersDrop/Drop_DiscountTax/issues) 

## License
[GNU General Public License, version 3 (GPLv3)] http://opensource.org/licenses/gpl-3.0

## Copyright
(C) 2019 Drop S.R.L.

## TODO
- Make it work in frontend (customer account + sales email of order/invoice/creditmemo)
- Make it compatible with Eadesigndev_Pdfgenerator
