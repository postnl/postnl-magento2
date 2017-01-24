#!/usr/bin/env bash

set -x

if [ "$CODE_COVERAGE" = "false" ]; then
    phpenv config-rm xdebug.ini
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
