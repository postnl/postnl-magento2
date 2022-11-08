<p align="center">
<img src="view/frontend/web/images/postnl-logo-large.png" alt="PostNL Logo" style="width:300px">
</p>

# PostNL Magento 2
[![Lastest Stable Version](https://img.shields.io/github/v/release/tig-nl/postnl-magento2?style=for-the-badge&color=3244b0)](https://github.com/tig-nl/postnl-magento2/releases/latest)
![TIG PostNL tested 2.3.7 versions](https://img.shields.io/badge/Tested%20with%20Magento-2.3.7-%2300cf00?style=for-the-badge)
![TIG PostNL tested 2.4.5 versions](https://img.shields.io/badge/Tested%20with%20Magento-2.4.5-%2300cf00?style=for-the-badge)
[![Total Extension downloads](https://img.shields.io/packagist/dt/tig/postnl-magento2?style=for-the-badge&color=ed7000)](https://packagist.org/packages/tig/postnl-magento2/stats)
![Build Status](https://img.shields.io/travis/tig-nl/postnl-magento2/master?style=for-the-badge)

This is the official PostNL Magento 2 extension to connect your Magento 2 webshop with PostNL.

## Requirements
- Magento version 2.3.6 - 2.3.7-p4, 2.4.3 - 2.4.5
- PHP 7.3+
- In order to use this extension you need to be a contract customer of PostNL. If you are not a customer of PostNL, you can <a href="https://www.postnl.nl/zakelijk/e-commerce/flexibele-bezorgopties" target="_blank" title="register at PostNL">register yourself here</a>.

## Installation 
We strongly recommend that you use a Staging Environment for the installation, and to also make a backup of your environment.

### Installation using composer (recommended)
To install the extension login to your environment using SSH. Then navigate to the Magento 2 root directory and run the following commands in the same order as described:
 
Enable maintenance mode:
~~~~shell
php bin/magento maintenance:enable
~~~~

1. Install the extension:
~~~~shell
composer require tig/postnl-magento2
~~~~

2. Enable the PostNL Magento 2 plugin
~~~~shell
php bin/magento module:enable TIG_PostNL
~~~~

3. Update the Magento 2 environment:
~~~~shell
php bin/magento setup:upgrade
~~~~

When your Magento environment is running in production mode, you also need to run the following comands:

4. Compile DI:
~~~~shell
php bin/magento setup:di:compile
~~~~

5. Deploy static content:
~~~~shell
php bin/magento setup:static-content:deploy
~~~~

6. Disable maintenance mode:
~~~~shell
php bin/magento maintenance:disable
~~~~

### Installation manually
1. Download the extension directly from [github](https://github.com/tig-nl/postnl-magento2) by clicking on *Code* and then *Download ZIP*.
2. Create the directory *app/code/TIG/PostNL* (Case-sensitive)
3. Extract the zip and upload the code into *app/code/TIG/PostNL*
4. Enable the PostNL Magento 2 plugin
~~~~shell
php bin/magento module:enable TIG_PostNL
~~~~

5. Update the Magento 2 environment:
~~~~shell
php bin/magento setup:upgrade
~~~~

## Update 
To update the PostNL Extension run the following commands:
~~~~shell
composer update tig/postnl-magento2
php bin/magento setup:upgrade
~~~~

## Uninstalling the PostNL extension

To remove the PostNL extension, simply make use of the uninstall command Magento provides: [https://devdocs.magento.com/guides/v2.4/install-gde/install/cli/install-cli-uninstall-mods.html#instgde-cli-uninst-mod-uninst](https://devdocs.magento.com/guides/v2.4/install-gde/install/cli/install-cli-uninstall-mods.html#instgde-cli-uninst-mod-uninst)

PostNL uses uninstall scripts. Please make sure to add the --remove-data flag to your command.
The uninstall script will ask if you would like to remove Order related PostNL data. Removing this data is optional.
The recommended uninstall command is:

~~~~shell
php bin/magento module:uninstall TIG_PostNL --backup-db --remove-data --clear-static-content`
~~~~

## Running tests (advanced)

Place this code in a working Magento 2 installation in the folder *app/code/TIG/PostNL* (Case-sensitive). 

Install all the dependencies:
- composer install
- npm install
- npm install -g grunt-cli
- Setup the integration tests as [advised by Magento](http://devdocs.magento.com/guides/v2.0/test/integration/integration_test_setup.html).
- Paste the following xml within the ``<testsuites>`` tag of **dev/tests/integration/phpunit.xml**:
~~~~xml
<testsuite name="TIG PostNL Integration Tests">
    <directory>../../../app/code/TIG/PostNL/Test/Integration</directory>
    <directory>../../../vendor/tig/postnl/Test/Integration</directory>
    <exclude>../../../app/code/Magento</exclude>
</testsuite>
~~~~

Run:
~~~~shell
grunt test
~~~~
This command will run the following tests:

- Unit tests.
- Integration tests.
- CodeSniffer ([PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md), [Object Calisthenics](https://github.com/object-calisthenics/phpcs-calisthenics-rules) and the [Magento 2 Extension Quality Program](https://github.com/magento/marketplace-eqp)).
- Lint all PHP files.
- JS lint all JS files.

The build status can be viewed on [Travis-ci.com](https://travis-ci.org/tig-nl/postnl-magento2)

## Support
This extension is developed by Total Internet Group ([TIG](https://tig.nl)) commissioned by PostNL.

### Extension basic configuration and account information
For questions related to your PostNL account and PostNL delivery options, please contact PostNL.
- **Phone:** +31 (0)88-2255651
- **Website:** [www.postnl.com](https://www.postnl.com)

### Extension support and advanced configuration
For questions about installing and configuring the extension please consult the relevant documentation:
- **Knowledge base:** [PostNL Magento 2 Knowledge base](https://tig-docs.atlassian.net/wiki/x/S4UH)
- **Frequently asked questions:** [FAQ](https://tig-docs.atlassian.net/wiki/x/S4UH)
- **Phone:** +31 (0)88-2255652
- **Email:** [digitaleklantsupport@postnl.nl](mailto:digitaleklantsupport@postnl.nl)

