# Postnl Magento 2

## Running tests

Place this code in an working Magento 2 installation in the folder app/code/TIG/PostNL (Case sensitive). Install all the dependencies:

- composer install
- npm install
- npm install -g grunt-cli
- Setup the integration tests as [advised by Magento](http://devdocs.magento.com/guides/v2.0/test/integration/integration_test_setup.html).
- Paste the following xml within the <testsuites> tag of dev/tests/integration/phpunit.xml:
<testsuite name="TIG PostNL Integration Tests">
    <directory>../../../app/code/TIG/PostNL/Test/Integration</directory>
    <directory>../../../vendor/tig/postnl/Test/Integration</directory>
    <exclude>../../../app/code/Magento</exclude>
</testsuite>

Run:

`grunt test`

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
