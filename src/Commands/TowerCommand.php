<?php declare(strict_types=1);

namespace DIW\Commands;

use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class TowerCommand implements CommandInterface
{

    public static function command(Application $app): void
    {
        $app->command('tower', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);

            $io->writeln('Opening git tower');
            if (!\file_exists('/Applications/Tower.app/Contents/MacOS/gittower')) {
                $io->error('tower command not found. Please install git tower by following the instructions provided here: https://www.git-tower.com/help/mac/integration/cli-tool');

                return;
            }

            $output = runAsUser('/Applications/Tower.app/Contents/MacOS/gittower $(git rev-parse --show-toplevel)');

            if (\strpos($output, 'fatal: Not a git repository') !== false) {
                $io->error('Could not find git directory');
            }
        })->descriptions('Open closest git project in Tower');
    }
}
