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
    public static function command(Application $app): void
    {
        $app->command('xdebug [-e|--enable] [-d|--disable]', function ($enable, $disable, InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);
            if (!isDockerRunning($io)) {
                return;
            }

            $containerSuffix = $_ENV['docker']['container']['suffix'];

            # output only the ID for the container name *__shop
            # remove whitespaces etc
            $xdebugContainerID = removeSpaces(run(\sprintf('docker ps -aqf "name=%s$"', $containerSuffix)));

            if (!$enable && !$disable) {
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
            'Enable or disable xdebug on the *__shop container (Default: status)', [
                '--enable' => 'Enables xdebug',
                '--disable' => 'Disables xdebug'
            ]
        );
    }
}
