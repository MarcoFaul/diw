<?php declare(strict_types=1);

namespace DIW\Commands;

use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class DocCommand implements CommandInterface
{

    public static function command(Application $app): void
    {
        $app->command('doc', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);

            $io->writeln('Opening documentation');

            runAsUser('open https://diw-tool.netlify.app/');
        })->descriptions('Opens documentation within a browser');
    }
}
