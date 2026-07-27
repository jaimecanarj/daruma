<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config
set('repository', 'https://github.com/jaimecanarj/daruma.git');
set('keep_releases', 5);
set('http_user', getenv('DEPLOY_REMOTE_USER'));
set('writable_mode', 'chmod');
set('env', [
    'PATH' => getenv('DEPLOY_SERVER_PATH'),
]);
set('bin/php', 'php');
set('bin/composer', 'composer');

// Hosts
host('staging')
    ->setHostname(getenv('DEPLOY_HOSTNAME'))
    ->setRemoteUser(getenv('DEPLOY_REMOTE_USER'))
    ->setIdentityFile(getenv('DEPLOY_IDENTITY_FILE') ?: '~/.ssh/deploy_daruma_dev')
    ->setPort((int) getenv('DEPLOY_PORT'))
    ->set('branch', 'dev')
    ->setDeployPath(getenv('DEPLOY_PATH'));

// Tasks
task('build', function () {
    run('cd {{release_path}} && npm install --include=dev && npm run build && rm -rf node_modules');
});

// Hooks
after('deploy:vendors', 'build');
after('deploy:failed', 'deploy:unlock');
