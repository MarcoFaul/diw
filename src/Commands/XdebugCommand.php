<?php declare(strict_types=1);

namespace DIW\Commands;


use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


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
            $xdebugContainerID = removeSpaces(execCommand(\sprintf('docker ps -aqf "name=%s$"', $containerSuffix)));

            if (!$enable && !$disable) {
                $io->note(execCommand(\sprintf('docker exec %s bash -c "cd ~/ && make status"', $xdebugContainerID)));

                return;
            }

            $xdebugEnabled = execCommand(\sprintf('docker exec %s bash -c "php -v | grep Xdebug"', $xdebugContainerID)) !== null;

            if ($xdebugEnabled && $enable) {
                $io->error('Xdebug is already active');

                return;
            }

            if (!$xdebugEnabled && !$enable) {
                $io->error('Xdebug is already inactive');

                return;
            }

            if ($enable) {
                execCommand(\sprintf('docker exec %s bash -c "cd ~/ && make xdebug-on"', $xdebugContainerID));
                $io->success('Successfully activated xdebug');
            } else {
                execCommand(\sprintf('docker exec %s bash -c "cd ~/ && make xdebug-off"', $xdebugContainerID));

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
