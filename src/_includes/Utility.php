<?php declare(strict_types=1);

use SebastianBergmann\Version;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

define('APP_NAME', 'DIW');

/**
 * Output a table to the console.
 *
 * @param array $headers
 * @param array $rows
 *
 * @return void
 */
function table(array $headers = [], array $rows = [])
{
    $table = new Table(new ConsoleOutput);

    $table->setHeaders($headers)->setRows($rows);

    $table->render();
}

/**
 * Output the given text to the console.
 *
 * @param string $output
 *
 * @return void
 */
function output(string $output)
{
    if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'testing') {
        return;
    }

    (new ConsoleOutput)->writeln($output);
}

/**
 * Get the user
 */
function user(): string
{
    if (isset($_SERVER['SUDO_USER']) && $_SERVER['SUDO_USER'] !== null) {
        return $_SERVER['SUDO_USER'];
    }

    if (isset($_SERVER['USER']) && $_SERVER['USER'] !== null) {
        return $_SERVER['USER'];
    }

    return '';
}

/**
 * get current version based on git describe and tags
 *
 * @return Version
 */
function version(): Version
{
    $version = \trim(\file_get_contents(__DIR__ . '/../../version'));

    return new Version($version, __DIR__ . '/../../');
}

function isDockerRunning(): bool
{
    return !(runCommand('docker info >/dev/null 2>&1;') !== '');
}
