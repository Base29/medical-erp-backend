<?php
namespace Deployer;

require 'contrib/rsync.php';
require 'recipe/laravel.php';

// Config
// set('repository', 'git@github.com:IT-Roadway-Ltd/ESM-BackEnd.git');
set('repository', trim(`git config --get remote.origin.url`));

set('application', 'esm'); //for your reference
set('ssh_multiplexing', true); // Speed up deployment

set('rsync_src', function () {
    return __DIR__; // If your project isn't in the root, you'll need to change this.
});

// Files you don't want in your production server.
add('rsync', [
    'exclude' => [
        '.env',
        '.git',
        '/storage/',
        '/vendor/',
        '/node_modules/',
        '.github',
        'deploy.php',
        'upload-esm-backend.sh',
        '.vscode',
        '.DS_Store',
        '.editorconfig',
    ],
]);

// Hosts
host('3.232.244.22')
    ->setRemoteUser('ubuntu') // SSH user
    ->setIdentityFile('~/.ssh/ESM_TESTVM_New.pem')
    ->set('writable_use_sudo', true)
    ->setDeployPath('/var/www/esm/backend');

after('deploy:failed', 'deploy:unlock'); // In case your deployment goes wrong

desc('Deploy the application');
task('deploy', [
    'deploy:prepare',
    'rsync',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:optimize',
    'artisan:optimize:clear',
    'artisan:migrate',
    'deploy:publish',
]);