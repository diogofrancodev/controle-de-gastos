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

     task('deploy', [
        'deploy:info',
        'deploy:prepare',
        'deploy:vendors',
        'artisan:storage:link',
        'artisan:view:cache',
        'artisan:config:cache',
        'artisan:migrate',
        'npm:install',
        'npm:run:prod',
        'artisan:optimize',
        'deploy:publish',
        'php-fpm:reload',

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
        run('npm install;npm run dev;');
    });

    after('artisan:optimize', 'artisan:config:clear');
    after('artisan:config:clear', 'artisan:migrate');

    after('deploy:failed', 'deploy:unlock');
