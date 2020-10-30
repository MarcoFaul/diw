<?php declare(strict_types=1);

namespace DIW\Commands;


use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class DevCommand implements CommandInterface
{
    public static function command(Application $app): void
    {
        $app->command('dev', function (InputInterface $input, OutputInterface $output) {
            //@TODO:
            info('Successfully prepared for local development');
        })->descriptions('Installs all dependencies');
    }
}
