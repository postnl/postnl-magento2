#!/usr/bin/env bash

set -x

if [ "$CODE_COVERAGE" = "false" ]; then
    phpenv config-rm xdebug.ini
else
    # Currently there is a bug in the pecl installer causing a segmentation fault when updating while
    # an extension is enabled: https://bugs.xdebug.org/view.php?id=1729
    sed -i 's/zend_/;zend_/g' $(php -i | grep 'xdebug.ini' |  sed 's/^.*=> //')
    # Pecl automatically installs xdebug 3.x, but Magento doesn't support it yet. Use this to downgrade.
    pecl install xdebug-2.9.8

    sed -i 's/;zend_/zend_/g' $(php -i | grep 'xdebug.ini' |  sed 's/^.*=> //')
fi

which phpcs
if [ $? != "0" ]; then
    composer global require "squizlabs/php_codesniffer=*"
fi

composer install

npm install

which grunt
if [ $? != "0" ]; then
    npm install grunt-cli -g
fi
