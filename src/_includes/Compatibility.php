<?php declare(strict_types=1);

$dir = __DIR__;

# those scripts are used within the github build workflow
$inTestingEnvironment = \in_array($_SERVER['SCRIPT_NAME'], ['vendor/bin/phpcs', 'vendor/bin/phpunit', 'vendor/bin/phpcbf']) === true;
if (PHP_OS !== 'Darwin' && !$inTestingEnvironment) {
    error(APP_NAME . ' only supports the Mac operating system.' . PHP_EOL);

    exit(1);
}

# read composer.json the get min php version
$minPHPVersion = \str_replace('>=', '', \json_decode(\file_get_contents(__DIR__ . '/../../composer.json'), true)['require']['php']);

if (PHP_VERSION_ID <= 70200) {
    error(APP_NAME . ' requires PHP 7.2 or later.');

    exit(1);
}

# doesn't make sense to check for brew when we installed it via brew
#if (!$inTestingEnvironment && run('which brew') === '') {
#    error(APP_NAME . ' requires Homebrew to be installed on your Mac.');
#
#    exit(1);
#}
