<?php
namespace Deployer;

require_once 'recipe/common.php';

// Project name
set('application', 'animerss');

// Project repository
set('repository', 'git@github.com:buitensportdurf/runtracker-api.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
set('shared_dirs', ['var/log', 'var/sessions']);
set('shared_files', ['.env.local']);
//set('writable_dirs', ['var']);
set('migrations_config', '');
set('allow_anonymous_stats', false);

// Hosts
host('runtracker.loken.nl')
    ->user('www-data')
    ->set('branch', function () {
        return input()->getOption('branch') ?: 'master';
    })
    ->set('deploy_path', '/var/www/api.survivalruns.nl');

set('bin/console', function () {
    return parse('{{release_path}}/bin/console');
});

set('console_options', function () {
    return '--no-interaction';
});

desc('Migrate database');
task('database:migrate', function () {
    $options = '--allow-no-migration';
    if (get('migrations_config') !== '') {
        $options = sprintf('%s --configuration={{release_path}}/{{migrations_config}}', $options);
    }

    run(sprintf('{{bin/php}} {{bin/console}} doctrine:migrations:migrate %s {{console_options}}', $options));
});

desc('Clear cache');
task('deploy:cache:clear', function () {
    run('{{bin/php}} {{bin/console}} cache:clear {{console_options}} --no-warmup');
});

desc('Warm up cache');
task('deploy:cache:warmup', function () {
    run('{{bin/php}} {{bin/console}} cache:warmup {{console_options}}');
});

desc('Deploy project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'deploy:cache:clear',
    'deploy:cache:warmup',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);

after('deploy', 'success');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');

