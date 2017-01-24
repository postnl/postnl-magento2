#!/usr/bin/env bash

set -e
set -x

if [ "$CODE_COVERAGE" = "true" ]; then
    # Collect code coverage and send it to Coveralls
    mv /tmp/magento2/vendor/tig/postnl/build/ ${TRAVIS_BUILD_DIR}

    mv /tmp/magento2/dev/tests/integration/build/logs/clover-integration.xml ${TRAVIS_BUILD_DIR}/build/logs/

    sed -i -e "s|/tmp/magento2/vendor/tig/postnl/|${TRAVIS_BUILD_DIR}/|g" ${TRAVIS_BUILD_DIR}/build/logs/clover-*.xml

    php vendor/bin/coveralls -v
fi
