#!/usr/bin/env bash

set -e
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

if [ "$CODE_COVERAGE" = "true" ]; then
    # Collect code coverage and send it to Coveralls
    mkdir -p ${TRAVIS_BUILD_DIR}/build/logs/

    mv /tmp/magento2/dev/tests/integration/build/logs/clover.xml ${TRAVIS_BUILD_DIR}/build/logs/

    sed -i -e "s|/tmp/magento2/vendor/tig/postnl/|${TRAVIS_BUILD_DIR}/|g" ${TRAVIS_BUILD_DIR}/build/logs/clover.xml

    php vendor/bin/coveralls -v
fi
