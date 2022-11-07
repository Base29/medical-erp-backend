<?php
namespace Deployer;

require 'contrib/rsync.php';

// Include the Laravel & rsync recipes
require 'recipe/laravel.php';

set('application', 'dep-demo'); //for your reference
set('ssh_multiplexing', true); // Speed up deployment

set('rsync_src', function () {
    return __DIR__; // If your project isn't in the root, you'll need to change this.
});

// Files you don't want in your production server.
add('rsync', [
    'exclude' => [
        '.git',
        '/storage/',
        '/vendor/',
        '/node_modules/',
        '.github',
        'deploy.php',
        'upload-esm-backend.sh',
        '.vscode',
    ],
]);

// Hosts
host('3.232.244.22')
    ->setRemoteUser('your_SSH_user') // SSH user
    ->setDeployPath('/var/www/website') // Deploy path
    ->setIdentityFile('~/.ssh/ESM_TESTVM_New'); // Your SSH key

after('deploy:failed', 'deploy:unlock'); // In case your deployment goes wrong

desc('Deploy the application');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:secrets',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'php-fpm:restart',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:optimize',
    'artisan:migrate',
    'deploy:symlink',
    'deploy:unlock',
    'deploy:cleanup',
]);