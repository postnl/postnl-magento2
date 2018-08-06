# Postnl Magento 2

[![Build Status](https://travis-ci.org/tig-nl/postnl-magento2.svg?branch=master)](https://travis-ci.org/tig-nl/postnl-magento2) ![Coverage Status](https://coveralls.io/repos/github/tig-nl/tig-extension-tig-postnl-magento2/badge.svg?t=uuXzu3)

## Installation

We strongly recommend you to use a Staging Environment for the installation and to make a backup of your environment.

To install the extension login to your environment using SSH. Then navigate to the Magento 2 Root and run the following commands in the same order as described:
 
Enable maintenance mode:
~~~~
php bin/magento maintenance:enable
~~~~

Install the extension:
~~~~
composer require tig/postnl-magento2
~~~~

Empty the following folders if they exists (Make sure to not delete the folders):
- var/cache
- var/di
- var/generation
- var/pagecache
- var/view_preprocessed

Flush the cache:
~~~~
php bin/magento cache:flush
~~~~

Update the Magento 2 environment:
~~~~
php bin/magento setup:upgrade
~~~~

Compile DI:
~~~~
php bin/magento setup:di:compile
~~~~

Deploy static content:
~~~~
php bin/magento setup:static-content:deploy
~~~~

Re-index the Magento 2 environment:
~~~~
php bin/magento indexer:reindex
~~~~

Disable maintenance mode:
~~~~
php bin/magento maintenance:disable
~~~~

The installation on your Staging Environment is now finished.

## User and Configuration Manual
https://servicedesk.tig.nl/hc/nl/articles/115001935267

## Full installation Manual
https://servicedesk.tig.nl/hc/nl/articles/115001631247

## Knowledge Base
https://servicedesk.tig.nl/hc/nl/categories/115000341267

## Running tests (advanced)

Place this code in a working Magento 2 installation in the folder app/code/TIG/PostNL (Case-sensitive). Install all the dependencies:

- composer install
- npm install
- npm install -g grunt-cli
- Setup the integration tests as [advised by Magento](http://devdocs.magento.com/guides/v2.0/test/integration/integration_test_setup.html).
- Paste the following xml within the ``<testsuites>`` tag of **dev/tests/integration/phpunit.xml**:
~~~~
<testsuite name="TIG PostNL Integration Tests">
    <directory>../../../app/code/TIG/PostNL/Test/Integration</directory>
    <directory>../../../vendor/tig/postnl/Test/Integration</directory>
    <exclude>../../../app/code/Magento</exclude>
</testsuite>
~~~~

Run:

`grunt test`

This command will run the following tests:

- Unit tests.
- Integration tests.
- CodeSniffer ([PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md), [Object Calisthenics](https://github.com/object-calisthenics/phpcs-calisthenics-rules) and the [Magento 2 Extension Quality Program](https://github.com/magento/marketplace-eqp)).
- Lint all PHP files.
- JS lint all JS files.

The build status can be viewed on [Travis-ci.com](https://travis-ci.org/tig-nl/postnl-magento2)

## Frontend: Changing Colors of the PostNL extension

Open: **app/code/TIG/PostNL/view/frontend/web/css/source/deliveryoptions.less**

Copy the variables to your own **theme.less** or extend them in your **extend.less**. More information:
http://devdocs.magento.com/guides/v2.0/frontend-dev-guide/css-guide/css_quick_guide_approach.html#simple_override
