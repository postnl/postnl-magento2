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

CACHE_DIR="${HOME}/.download_cache/"
BUILD_DIR="/tmp/magento2"

if [ -z $TRAVIS_BUILD_DIR ]; then TRAVIS_BUILD_DIR=`pwd`; fi
if [ -z $TRAVIS_COMMIT ]; then TRAVIS_COMMIT=`git rev-parse HEAD`; fi
if [ -z $MAGENTO_VERSION ]; then MAGENTO_VERSION="2.1.3"; fi
if [ -z $MAGENTO_DB_HOST ]; then MAGENTO_DB_HOST="localhost"; fi
if [ -z $MAGENTO_DB_PORT ]; then MAGENTO_DB_PORT="3306"; fi
if [ -z $MAGENTO_DB_USER ]; then MAGENTO_DB_USER="root"; fi
if [ -z $MAGENTO_DB_PASS ]; then MAGENTO_DB_PASS=""; fi
if [ -z $MAGENTO_DB_NAME ]; then
    MAGENTO_DB_NAME="magento";
fi

CACHE_FILE="${CACHE_DIR}magento-${MAGENTO_VERSION}.tar.gz"

MYSQLPASS=""
if [ ! -z $MAGENTO_DB_PASS ]; then MYSQLPASS="-p${MAGENTO_DB_PASS}"; fi

mkdir -p ${BUILD_DIR}
mkdir -p ${CACHE_DIR}

if [ ! -f "$CACHE_FILE" ]; then
    wget "http://magento.mirror.hypernode.com/releases/magento-${MAGENTO_VERSION}.tar.gz" -O $CACHE_FILE
fi

tar xzf $CACHE_FILE -C /tmp/magento2

find Test/Fixtures -type f -print0 | xargs -0 -n 1 sed -i -e "s/MAGENTO_DB_HOST/${MAGENTO_DB_HOST}/g"
find Test/Fixtures -type f -print0 | xargs -0 -n 1 sed -i -e "s/MAGENTO_DB_PORT/${MAGENTO_DB_PORT}/g"
find Test/Fixtures -type f -print0 | xargs -0 -n 1 sed -i -e "s/MAGENTO_DB_USER/${MAGENTO_DB_USER}/g"
find Test/Fixtures -type f -print0 | xargs -0 -n 1 sed -i -e "s/MAGENTO_DB_PASS/${MAGENTO_DB_PASS}/g"
find Test/Fixtures -type f -print0 | xargs -0 -n 1 sed -i -e "s/MAGENTO_DB_NAME/${MAGENTO_DB_NAME}/g"

cp -v Test/Fixtures/env.php "${BUILD_DIR}/app/etc/env.php"
cp -v Test/Fixtures/config.php "${BUILD_DIR}/app/etc/config.php"
cp -v Test/Fixtures/install-config-mysql.php "${BUILD_DIR}/dev/tests/integration/etc/install-config-mysql.php"
cp -v Test/Fixtures/phpunit.xml "${BUILD_DIR}/dev/tests/integration/phpunit.xml"

zip --exclude=node_modules/* --exclude=vendor/* --exclude=.git/* -r build.zip .

REPOSITORY_CONFIG="{\"type\": \"package\",\"package\": { \"name\": \"tig/postnl\", \"version\": \"master\", \"dist\": {\"type\": \"zip\",\"url\": \"${TRAVIS_BUILD_DIR}/build.zip\",\"reference\": \"master\" }, \"autoload\": {\"files\": [\"registration.php\"],\"psr-4\": {\"TIG\\\\PostNL\\\\\": \"\"}},\"require\": {\"setasign/fpdf\": \"1.8.1\",\"setasign/fpdi\": \"1.6.1\"}}}"

if [ -d "$HOME/.cache/composer/files/tig/" ]; then
    rm -rf $HOME/.cache/composer/files/tig/;
fi

( cd "${BUILD_DIR}/" && composer config minimum-stability dev )
( cd "${BUILD_DIR}/" && composer config repositories.postnl "${REPOSITORY_CONFIG}" )
( cd "${BUILD_DIR}/" && composer require tig/postnl --ignore-platform-reqs )

mysql -u${MAGENTO_DB_USER} ${MYSQLPASS} -h${MAGENTO_DB_HOST} -P${MAGENTO_DB_PORT} -e "DROP DATABASE IF EXISTS \`${MAGENTO_DB_NAME}\`; CREATE DATABASE \`${MAGENTO_DB_NAME}\`;"

chmod 777 "${BUILD_DIR}/var/"
chmod 777 "${BUILD_DIR}/pub/"
chmod 777 "${BUILD_DIR}/vendor/phpunit/phpunit/phpunit"

( cd ${BUILD_DIR} && php -d memory_limit=2048M bin/magento setup:install \
    --admin-firstname=TIG \
    --admin-lastname=support \
    --admin-email=sd@tig.nl \
    --admin-user=TIGsupport \
    --admin-password=asdfasdf1 \
    --base-url="http://magento.local/" \
    --backend-frontname=admin \
    --db-host="${MAGENTO_DB_HOST}" \
    --db-name="${MAGENTO_DB_NAME}" \
    --db-user="${MAGENTO_DB_USER}" \
    --db-password="${MAGENTO_DB_PASS}" \
    --language=nl_NL \
    --currency=EUR \
    --timezone=Europe/Amsterdam \
    --use-rewrites=1 \
    --session-save=db )

# Integration tests require to have no base url as they will fail otherwise.
mysql magento -e 'delete from core_config_data where path = "web/secure/base_url"'

( cd ${BUILD_DIR} && php -d memory_limit=2048M bin/magento setup:upgrade )
