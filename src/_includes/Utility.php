<?php declare(strict_types=1);

use DIW\Components\Helper\Version;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

define('APP_NAME', 'DIW');
define('CONTAINER_SUFFIX', '__shop');

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
    return new Version();
}

function isDockerRunning(SymfonyStyle $io): bool
{
    $isDockerRunning = !(quietly('docker info') !== '');
    if (!$isDockerRunning) {
        $io->error('Docker is not running. Please (re)start Docker.');

        return false;
    }

    return true;
}

/**
 * Output the given text to the console.
 * Should be used only for compatibility methods
 *
 * @param string $output
 *
 * @return void
 */
function error(string $output)
{
    output('<fg=red>' . $output . '</>');
}

function removeSpaces(string $string): string
{
    return \str_replace(["\r", "\n"], '', $string);
}
