# Postnl Magento 2

## Installation

We strongly recommend to use a Staging Environment for the installation and to back up the system before installation.

Login using SSH and go to the Magento 2 Root.
Run the following commands to install the extension.
- composer require tig/postnl-magento2

Empty the following folders (Make sure to not delete the folders)
- var/cache
- var/pagecache
- var/generation
- var/di (if this folder exists)

Flush the cache using the following command.
- php bin/magento cache:flush

Update the Magento 2 environment using the following command
- php bin/magento setup:upgrade

Re-index the Magento 2 environment using the following command
- php bin/magento indexer:reindex

The installation on your Staging Environment is now finished

## User and Configuration Manual
https://tig.zendesk.com/knowledge/articles/115001935267

## Full installation Manual
https://servicedesk.tig.nl/hc/nl/articles/115001631247

## Knowledge Base
https://servicedesk.tig.nl/hc/nl/categories/115000341267

## Running tests

Place this code in a working Magento 2 installation in the folder app/code/TIG/PostNL (Case-sensitive). Install all the dependencies:

- composer install
- npm install
- npm install -g grunt-cli
- Setup the integration tests as [advised by Magento](http://devdocs.magento.com/guides/v2.0/test/integration/integration_test_setup.html).
- Paste the following xml within the <testsuites&gt; tag of dev/tests/integration/phpunit.xml:
'''
<testsuite name="TIG PostNL Integration Tests"&gt;
<directory&gt;../../../app/code/TIG/PostNL/Test/Integrationdirectory&gt;
<directory&gt;../../../vendor/tig/postnl/Test/Integrationdirectory&gt;
<exclude&gt;../../../app/code/Magentoexclude&gt;
testsuite&gt;
'''

Run:

'grunt test'

This command will run the following tests:

- Unit tests.
- Integration tests.
- CodeSniffer ([PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md), [Object Calisthenics](https://github.com/object-calisthenics/phpcs-calisthenics-rules) and the [Magento 2 Extension Quality Program](https://github.com/magento/marketplace-eqp)).
- Lint all PHP files.
- JS lint all JS files.

The build status can be viewed on [Travis-ci.com](http://travis-ci.com)

## Frontend: Changing Colors of the PostNL extension

Open: app/code/TIG/PostNL/view/frontend/web/css/source/deliveryoptions.less

Copy the variables to your own theme.less or extend them in your extend.less. More information:
http://devdocs.magento.com/guides/v2.0/frontend-dev-guide/css-guide/css_quick_guide_approach.html#simple_override