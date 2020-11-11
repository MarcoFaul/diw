<?php declare(strict_types=1);

namespace DIW\Commands;


use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class XdebugCommand implements CommandInterface
{
    public const CONTAINER_USER = 'dev';

    public static function command(Application $app): void
    {
        $app->command('xdebug [-y|--enable]', function ($enable, InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);
            if (!isDockerRunning()) {
                $io->error('Docker is not running. Please (re)start Docker.');

                return;
            }

            $xdebugContainerName = run('docker ps --format {{.Names}} -f "name=__shop$"');
            $xdebugEnabled = run(\sprintf('docker exec -it -u %s %s bash -c "php -v | grep Xdebug"', XdebugCommand::CONTAINER_USER, $xdebugContainerName)) !== '';

            if (!$xdebugEnabled) {
                $io->error('Xdebug is not installed!');

                return;
            }

            if ($enable) {
                run(\sprintf('docker exec -u %s %s -c "cd ~/ && make xdebug-on"', XdebugCommand::CONTAINER_USER, $xdebugContainerName));
            } else {
                run(\sprintf('docker exec -u %s %s -c "cd ~/ && make xdebug-off"', XdebugCommand::CONTAINER_USER, $xdebugContainerName));
            }


            $io->note(run(\sprintf('docker exec -u %s %s -c "cd ~/ && make status"', XdebugCommand::CONTAINER_USER, $xdebugContainerName)));
        })->descriptions(
            'Enable or Disable xdebug on the shop container', [
                '--enable' => 'Enables xdebug',
            ]
        );
    }
}
