# Postnl Magento 2

## Running tests

Place this code in an working Magento 2 installation in the folder app/code/TIG/PostNL (Case sensitive). Install all the dependencies:

- composer install
- npm install
- npm install -g grunt-cli
- Setup the integration tests as [advised by Magento](http://devdocs.magento.com/guides/v2.0/test/integration/integration_test_setup.html).
 
Run:

`grunt test`

This command will run the following tests:

- Unit tests.
- Integration tests.
- CodeSniffer ([PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md), [Object Calisthenics](https://github.com/object-calisthenics/phpcs-calisthenics-rules) and the [Magento 2 Extension Quality Program](https://github.com/magento/marketplace-eqp)).
- Lint all PHP files.
- JS lint all JS files.

The build status can be viewed on [Travis-ci.com](http://travis-ci.com)
