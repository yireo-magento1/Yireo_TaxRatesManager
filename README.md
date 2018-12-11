# Yireo TaxRatesManager

Magento extension to keep EU tax rates in sync with online rates.

More information: https://www.yireo.com/software/magento-extensions/taxratesmanager

You can install this module in various ways:

1) Download the MagentoConnect package from our site and upload it into your own Magento Downloader application.

2) Download the Magento source archive from our site, extract the files and upload the files to your Magento root. Make sure to flush the Magento cache. Make sure to logout once you're done.

3) Use `modman` to install the git repository for you:

    modman init
    modman clone https://github.com/yireo/Yireo_TaxRatesManager
    modman update Yireo_TaxRatesManager

4) Use `composer` to install the composer package for you. See below.

## Instructions for using composer

Use composer to install this extension. First make sure to initialize composer with the right settings:

    composer -n init
    composer install --no-dev

Next, modify your local composer.json file:

    @todo

Make sure to set the `magento-root-dir` properly. Test this by running:

    composer update --no-dev

Done.

## Tests
This extension ships with some unit tests and some functional tests. Do make sure to run the functional tests only on a testing site. The tests may modify the tax rates and other settings in your database.
