module.exports = function (grunt) {
    var magento2path = '../../../../';
    var phpunitXmlPath = __dirname + '/phpunit.xml';

    if (grunt.file.isDir('/tmp/magento2/')) {
        magento2path = '/tmp/magento2/';
        phpunitXmlPath = '/tmp/magento2/vendor/tig/postnl/phpunit.xml.dist';
    }

    var phpcsCommand = 'php -ddisplay_errors=1 ~/.composer/vendor/bin/phpcs -v --standard=phpcs.xml ' +
        '--runtime-set installed_paths ' +
        'vendor/squizlabs/php_codesniffer/CodeSniffer/Standards,' + '' +
        'vendor/magento/marketplace-eqp,' + '' +
        'vendor/object-calisthenics/phpcs-calisthenics-rules/src/ ';

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        exec: {
            phpcsEasy: phpcsCommand + '--severity=10 .',
            phpcsFull: phpcsCommand + ' .',
            unitTests: 'cd ' + magento2path + ' && vendor/bin/phpunit -c "' + phpunitXmlPath + '"',
            integrationTests:
                'cd ' + magento2path + 'dev/tests/integration &&' +
                '../../../vendor/bin/phpunit --testsuite "TIG PostNL Integration Tests"',
            phplint: 'find . -name "*.php" ! -path "./vendor/*" -print0 | xargs -0 -n 1 -P 8 php -l',
            translations_nl: '../../../../bin/magento i18n:collect-phrases -vvv . -o i18n/nl_NL.csv',
            translations_en: '../../../../bin/magento i18n:collect-phrases -vvv . -o i18n/en_US.csv'
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

    // Default task(s).
    grunt.registerTask('test', ['jshint:all', 'exec:phplint', 'exec:phpunit', 'exec:phpcs']);
    grunt.registerTask('translations', ['exec:translations_nl', 'exec:translations_en']);
    grunt.registerTask('lint', ['exec:phplint', 'jshint:all']);
    grunt.registerTask('phpcs', ['exec:phpcsFull']);
    grunt.registerTask('test', [
        'exec:unitTests',
        'exec:integrationTests',
        'exec:phpcsEasy',
        'exec:phplint',
        'jshint:all'
    ]);
    grunt.registerTask('default', []);

};
