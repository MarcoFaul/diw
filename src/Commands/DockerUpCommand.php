<?php declare(strict_types=1);

namespace DIW\Commands;


use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class DockerUpCommand implements CommandInterface
{
    public static function command(Application $app): void
    {
        $app->command('up', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);
            if(!isDockerRunning($io)) {
                return;
            }

            # remove all containers
            run('docker rm $(docker ps -aq) -f');

            # start composer file
            passthruCommand('docker-compose up -d');

        })->descriptions('Stops all running containers and executes the docker-compose file in the current directory');
    }
}
