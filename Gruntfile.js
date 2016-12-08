module.exports = function(grunt) {
    var phpunitPath = 'phpunit.xml';

    if (grunt.file.isDir('/tmp/magento2/')) {
        phpunitPath = '/tmp/magento2/vendor/tig/postnl/phpunit.xml.dist'
    }

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        exec: {
            phpcs: 'php -ddisplay_errors=1 ~/.composer/vendor/bin/phpcs --standard=phpcs.xml --extensions=php .',
            phpunit: 'phpunit -c "' + phpunitPath + '"'
        }
    });

    grunt.loadNpmTasks('grunt-exec');

    // Default task(s).
    grunt.registerTask('test', ['exec:phpcs', 'exec:phpunit']);
    grunt.registerTask('default', []);

};