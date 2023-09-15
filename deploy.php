<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/npm.php';
require 'contrib/php-fpm.php';

set('application', 'controle.diogofranco.com.br');
set('repository', 'git@github.com:diogofrancodev/controle-de-gastos.git');
set('http_user', 'www-data');
set('ssh_multiplexing', false);
set('git_tty', false);
set('default_timeout', 0);
set('php8.1-fpm', '8.1');

host('production')
    ->set('remote_user', 'root')
    ->set('hostname', '194.163.153.154')
    ->set('port', '4321')
    ->set('deploy_path', '/var/www/{{application}}/production')
    ->set('multiplexing', true);

    desc('Prepares a new release');
task('deploy:prepare', [
    'deploy:info',
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
]);

desc('Publishes the release');
task('deploy:publish', [
    'deploy:symlink',
    'deploy:unlock',
    'deploy:cleanup',
    'deploy:success',
]);

desc('Deploys your project');
task('deploy', [
    'deploy:prepare',
    'deploy:publish',
]);

    task('artisan:optimize', function () {
        run('echo comando optimize desativado');
    });

    task('artisan:config:clear', function () {
        cd('{{release_path}}');
        run('php artisan config:clear');
    });

    task('composer:dumpautoload', function () {
        cd('{{release_path}}');
        run('composer dumpautoload');
    });

    task('npm:run:prod', function () {
        cd('{{release_path}}');
        run('npm install;npm run production;');
    });

    after('artisan:optimize', 'artisan:config:clear');
    after('artisan:config:clear', 'artisan:migrate');

    after('deploy:failed', 'deploy:unlock');
