<?php
/**
 * w-vision
 *
 * LICENSE
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that is distributed with this source code.
 *
 * @copyright  Copyright (c) 2018 w-vision AG (https://www.w-vision.ch)
 */

namespace Deployer;

require __DIR__ . '/vendor/deployer/deployer/recipe/symfony3.php';  // Comes form deployer.phar
require __DIR__ . '/vendor/w-vision/pimcore-deployer/recipes/pimcore.php';

host('ec2-18-197-191-63.eu-central-1.compute.amazonaws.com')
    ->user('pimcore')
    ->port(22)
    ->set('deploy_path', '/home/pimcore')
    ->identityFile('.ssh/aws_key.pem')
    ->stage('dev')
    ->set('branch', 'master');

set('writable_use_sudo', true);

// Can be done via http as well.
set('repository', 'https://github.com/AxelRueweler/demo-ecommerce.git');

// There may be some files or directories missing.
set('default_stage', 'dev');
set('shared_files', [
    'app/config/parameters.yml',
    'var/config/system.php',
    'var/config/debug-mode.php',
    'var/config/maintenance.php',
    'var/config/GeoLite2-City.mmdb',
    'var/config/perspectives.php',
    'var/config/customviews.php'
]);
set('shared_dirs', [
    'web/var',
    'var/email',
    'var/recyclebin',
    'var/versions',
    'var/sessions'
]);
// The configuration files of pimcore that will be processed at creation
set('pimcore_shared_configurations', [
    'var/config/website-settings.php',
    'var/config/reports.php',
    'var/config/web2print.php',
    'var/config/workflowmanagement.php'
]);

set('writable_dirs', [
    'web/var',
    'var/email',
    'var/recyclebin',
    'var/versions',
    'var/sessions',
    'var/config'
]);

// If your PHP executable is installed within a non standard path, use this:
/*set('bin/php', function () {
    return '/opt/cpanel/ea-php70/root/usr/bin/php';
});*/

// If you want to use a custom composer file, use this
/*set('bin/composer', function() {
    return '{{bin/php}} composer.phar';
});*/

desc('Deploy your project');
task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:assets:install',
    'deploy:pimcore:migrate:core',
    'deploy:pimcore:migrate',
    'deploy:clear_paths',
    'deploy:pimcore:rebuild-classes',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
])->desc('Deploy your project');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
