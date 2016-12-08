module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        exec: {
            phpcs: 'php -ddisplay_errors=1 ~/.composer/vendor/bin/phpcs --standard=phpcs.xml --extensions=php .',
            phpunit: 'phpunit'
        }
    });

    grunt.loadNpmTasks('grunt-exec');

    // Default task(s).
    grunt.registerTask('test', ['exec:phpcs', 'exec:phpunit']);
    grunt.registerTask('default', []);

};