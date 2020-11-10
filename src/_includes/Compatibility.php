<?php declare(strict_types=1);

/**
 * Check the system's compatibility.
 */
$inTestingEnvironment = \strpos($_SERVER['SCRIPT_NAME'], 'phpunit') !== false;

if (PHP_OS !== 'Darwin' && ! $inTestingEnvironment) {
    echo APP_NAME . ' only supports the Mac operating system.' . PHP_EOL;

    exit(1);
}

# read composer.json the get min php version
$minPHPVersion = str_replace('>=', '', \json_decode(\file_get_contents(__DIR__ . '/../../composer.json'), true)['require']['php']);

if (PHP_VERSION_ID <= 70200) {
    echo APP_NAME . ' requires PHP 7.2 or later.';

    exit(1);
}

if (!$inTestingEnvironment && run('which brew') === '') {
    echo APP_NAME . ' requires Homebrew to be installed on your Mac.';

    exit(1);
}
