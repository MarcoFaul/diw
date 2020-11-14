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
        $app->command('xdebug [-e|--enable] [-s|--status]', function ($enable, $status, InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);
            if (!isDockerRunning($io)) {
                return;
            }

            # output only the ID for the container name *__shop
            $xdebugContainerID = run('docker ps -aqf "name=__shop$"');
            # remove whitespaces etc
            $xdebugContainerID = \str_replace(array("\r", "\n"), '', $xdebugContainerID);

            if ($status) {
                $io->note(run(\sprintf('docker exec %s bash -c "cd ~/ && make status"', $xdebugContainerID)));

                return;
            }

            $xdebugEnabled = run(\sprintf('docker exec %s bash -c "php -v | grep Xdebug"', $xdebugContainerID)) !== '';

            if ($xdebugEnabled && $enable) {
                $io->error('Xdebug is already active');

                return;
            }

            if (!$xdebugEnabled && !$enable) {
                $io->error('Xdebug is already inactive');

                return;
            }

            if ($enable) {
                run(\sprintf('docker exec %s bash -c "cd ~/ && make xdebug-on"', $xdebugContainerID));
                $io->success('Successfully activated xdebug');
            } else {
                run(\sprintf('docker exec %s bash -c "cd ~/ && make xdebug-off"', $xdebugContainerID));

                $io->success('Successfully deactivated xdebug');
            }

        })->descriptions(
            'Enable or Disable xdebug on the shop container', [
                '--enable' => 'Enables xdebug',
                '--status' => 'Shows the current xdebug status',
            ]
        );
    }
}
