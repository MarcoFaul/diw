<?php declare(strict_types=1);

namespace DIW\Commands;


use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class DockerShellCommand implements CommandInterface
{
    public static function command(Application $app): void
    {
        $app->command('shell', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);
            if(!isDockerRunning($io)) {
                return;
            }

            $return = run(' docker ps -f "name=__shop$"');

            $io->writeln($return);
        })->descriptions('Connects to the *__shop container');
    }
}
