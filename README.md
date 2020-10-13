# Postnl Magento 2

![TIG PostNL tested 2.2.x versions](https://img.shields.io/badge/Tested%20with-2.2.11-%23009f3e)
![TIG PostNL tested 2.3.x versions](https://img.shields.io/badge/Tested%20with-2.3.5-%23009f3e)
[![Build Status](https://travis-ci.org/tig-nl/postnl-magento2.svg?branch=master)](https://travis-ci.org/tig-nl/postnl-magento2) ![Coverage Status](https://coveralls.io/repos/github/tig-nl/tig-extension-tig-postnl-magento2/badge.svg?t=uuXzu3)

## Installation

We strongly recommend that you use a Staging Environment for the installation, and to also make a backup of your environment.

To install the extension login to your environment using SSH. Then navigate to the Magento 2 Roo Directory and run the following commands in the same order as described:
 
Enable maintenance mode:
~~~~
php bin/magento maintenance:enable
~~~~

Install the extension:
~~~~
composer require tig/postnl-magento2
~~~~

Empty the following folders if they exist (Make sure to not delete the folders):
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
https://confluence.tig.nl/display/SDPOSTNL/PostNL+Magento+2+extensie+gebruikershandleiding

## Full installation Manual
https://confluence.tig.nl/display/SDPOSTNL/PostNL+Magento+2+extensie+installatiehandleiding

## Knowledge Base
https://confluence.tig.nl/display/SDPOSTNL/PostNL+Magento+2+extensie

## Running tests (advanced)

Place this code in a working Magento 2 installation in the folder app/code/TIG/PostNL (Case-sensitive). 

Install all the dependencies:
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


## Uninstalling the PostNL extension

To remove the PostNL extension, simply make use of the uninstall command Magento provides: https://devdocs.magento.com/guides/v2.4/install-gde/install/cli/install-cli-uninstall-mods.html#instgde-cli-uninst-mod-uninst

PostNL uses uninstall scripts. Please make sure to add the --remove-data flag to your command.
The uninstall script will ask if you would like to remove Order related PostNL data. Removing this data is optional.
The recommended uninstall command is:

`bin/magento module:uninstall TIG_PostNL --backup-db --remove-data --clear-static-content`
