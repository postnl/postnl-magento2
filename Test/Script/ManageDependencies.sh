#!/usr/bin/env bash

set -x

#          ..::..
#     ..::::::::::::..
#   ::'''''':''::'''''::
#   ::..  ..:  :  ....::
#   ::::  :::  :  :   ::
#   ::::  :::  :  ''' ::
#   ::::..:::..::.....::
#     ''::::::::::::''
#          ''::''
#
#
# NOTICE OF LICENSE
#
# This source file is subject to the Creative Commons License.
# It is available through the world-wide-web at this URL:
# http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
# If you are unable to obtain it through the world-wide-web, please send an email
# to servicedesk@tig.nl so we can send you a copy immediately.
#
# DISCLAIMER
#
# Do not edit or add to this file if you wish to upgrade this module to newer
# versions in the future. If you wish to customize this module for your
# needs please contact servicedesk@tig.nl for more information.
#
# @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
# @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US

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
