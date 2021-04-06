<?php
namespace Deployer;

require 'recipe/composer.php';

set('shared_files', ['.env.local', 'public/robots.txt', 'public/.htaccess', 'public/.htusers']);
set('shared_dirs', ['var/data']);
inventory('hosts.yml');
after('deploy:failed', 'deploy:unlock');

desc('Upload the artifact to the production server');
task('deploy:upload', function () {
    upload(__DIR__ . '/build.zip', '{{release_path}}');
    run('cd {{release_path}} && unzip -q build.zip && rm build.zip');
});

desc('Clear cache');
task('deploy:cache:clear', function () {
    run('{{bin/php}} {{release_path}}/bin/console cache:clear --no-interaction');
});

desc('Deploy the project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:upload',
    'deploy:shared',
    'deploy:cache:clear',
    'deploy:writable',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);