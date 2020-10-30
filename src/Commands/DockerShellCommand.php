<?php declare(strict_types=1);

namespace DIW\Commands;


use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class DockerShellCommand implements CommandInterface
{
    public static function command(Application $app): void
    {
        $app->command('shell', function (InputInterface $input, OutputInterface $output) {
            if(!isDockerRunning()) {
                error('Docker is not running. Please (re)start Docker.');
                return;
            }

//            $text = run(' docker ps -f "name=__shop$"');
            $text = run(' docker ps -f "name=fik$"');

            $output->writeln($text);
        })->descriptions('Connects to the *__shop container');
    }
}
