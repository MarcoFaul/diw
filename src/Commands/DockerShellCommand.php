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

            $containerSuffix = $_ENV['docker']['container']['suffix'];
            $containerUser = $_ENV['docker']['container']['user'];

            $containerID = removeSpaces(execCommand(\sprintf('docker ps -aqf "name=%s$"', $containerSuffix)));

            if (!$containerID) {
                $io->error('No docker container found for the container suffix: ' . $containerSuffix);
                return;
            }

            if ($containerUser) {
                passthruCommand(\sprintf('docker exec -it -u %s %s bash', $containerUser, $containerID));
            } else {
                passthruCommand(\sprintf('docker exec -it %s bash', $containerID));
            }

        })->descriptions('Connects to the *__shop container');
    }
}
