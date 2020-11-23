<?php declare(strict_types=1);

namespace DIW\Commands;


use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class PhpstormCommand implements CommandInterface
{
    public static function command(Application $app): void
    {
        $app->command('phpstorm', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);

            $io->writeln('Opening PhpStorm');

            runAsUser('open -a PhpStorm ./');

        })->descriptions('Open closest git project in Tower');
    }
}
