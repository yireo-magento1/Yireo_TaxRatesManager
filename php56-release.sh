#!/bin/bash

# Linting check
find php56/ -type f -name \*.php -exec /usr/bin/php5.6 -l {} \; | grep -v 'No syntax'

cd php56/
version=`cat app/code/community/Yireo/TaxRatesManager/etc/config.xml | grep '<version>' | sed -n 's:.*<version>\(.*\)</version>.*:\1:p'`
file=Yireo_TaxRatesManager_php56_$version.zip

zip -qr9 ../$file .
