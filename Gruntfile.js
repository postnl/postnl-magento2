module.exports = function (grunt) {
    var magento2path = '../../../../';
    var phpunitXmlPath = __dirname + '/phpunit.xml';
    var buildPath = __dirname + '/Build/';

    if (grunt.file.isDir('/tmp/magento2/')) {
        magento2path = '/tmp/magento2/';
        phpunitXmlPath = '/tmp/magento2/vendor/tig/postnl/phpunit.xml.dist';
    }

    var phpcsCommand = 'php -ddisplay_errors=1 vendor/bin/phpcs -p ' +
        '--runtime-set installed_paths ' +
        'vendor/squizlabs/php_codesniffer/CodeSniffer/Standards,' +
        'vendor/magento/magento-coding-standard,' +
        'vendor/object-calisthenics/phpcs-calisthenics-rules/src/ ';

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        exec: {
            phpcs: phpcsCommand + '--standard=phpcs.xml  .',
            phpcsTest: phpcsCommand + '--standard=phpcs.test.xml --severity=10 Test',

            unitTests: 'cd ' + magento2path + ' && vendor/phpunit/phpunit/phpunit -c "' + phpunitXmlPath + '"',

            integrationTests:
                'cd ' + magento2path + 'dev/tests/integration &&' +
                'php -ddisplay_errors=1 ../../../vendor/phpunit/phpunit/phpunit --testsuite "TIG PostNL Tests"',

            ciTests:
                'cd ' + magento2path + 'dev/tests/integration &&' +
                'php -ddisplay_errors=1 ../../../vendor/phpunit/phpunit/phpunit',

            phraseTest:
                'cd ' + magento2path + ' && php bin/magento i18n:collect-phrases vendor/tig/postnl',

            phplint: 'if find . -name "*.php" ! -path "./vendor/*" -print0 | xargs -0 -n 1 -P 8 php -l | grep -v "No syntax errors detected"; then exit 1; fi',

            codeCoverage:
                'mkdir -p ' + buildPath + '/coverage/{unit,integration} && ' +
                'cd ' + magento2path + ' && ' +
                'vendor/bin/phpunit -c "' + phpunitXmlPath + '" --coverage-html ' + buildPath + '/coverage/unit && ' +
                'cd dev/tests/integration &&' +
                '../../../vendor/bin/phpunit --testsuite "TIG PostNL Integration Tests"  --coverage-html ' + buildPath + '/coverage/integration'
        },
        jshint: {
            all: [
                'view/frontend/web/js/**/*.js',
                'view/admihtml/web/js/**/*.js'
            ]
        },
        less: {
            deliveryoptions: {
                files: {
                    'view/frontend/web/css/deliveryoptions.css': 'view/frontend/web/css/source/deliveryoptions.less'
                }
            }
        },
        watch: {
            scripts: {
                files: ['view/frontend/web/css/source/**/*.less'],
                tasks: ['less:deliveryoptions'],
                options: {
                    livereload : true
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-exec');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-jshint');

    /**
     * Register the available tasks
     */
    grunt.registerTask('lint', 'Lint all PHP al JavaScript files', ['exec:phplint', 'jshint:all']);
    grunt.registerTask('phraseTest', 'Check if phrases are correct implemented.', ['exec:phraseTest']);
    grunt.registerTask('phpcs', 'Run the Code Sniffer: For all production code and for the test code', ['exec:phpcs', 'exec:phpcsTest']);
    grunt.registerTask('codeCoverage', 'Generate the code coverage report in build', ['exec:codeCoverage']);
    grunt.registerTask('runTests', 'Run all available tests: Unit and integration', ['exec:unitTests', 'exec:integrationTests']);
    grunt.registerTask('test', 'Run all code validation check: Unit tests, Code Sniffer, Linting, etc.', [
        'phpcs',
        'lint',
        'runTests'
    ]);
    grunt.registerTask('ci', 'This task is to be meant for running in CI only', [
        'phpcs',
        'lint',
        'exec:ciTests'
    ]);

    grunt.registerTask('default', 'Run the default task (test)', ['test']);

};
