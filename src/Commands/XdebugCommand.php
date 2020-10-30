<?php declare(strict_types=1);

namespace DIW\Commands;


use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class XdebugCommand implements CommandInterface
{
    public const CONTAINER_USER = 'dev:dev';

    public static function command(Application $app): void
    {
        $app->command('xdebug [-y|--enable]', function ($enable, InputInterface $input, OutputInterface $output) {
            if (!isDockerRunning()) {
                error('Docker is not running. Please (re)start Docker.');

                return;
            }

//            @TODO: change user

            $xdebugContainerName = run('docker ps --format {{.Names}} -f "name=__shop$"');
            $xdebugEnabled = run(\sprintf('docker exec -it -u %s %s bash -c "php -v | grep Xdebug"', XdebugCommand::CONTAINER_USER, $xdebugContainerName)) !== '';

            if (!$xdebugEnabled) {
                error('Xdebug is not installed!');

                return;
            }

            #@TODO: find right php.ini to change it (xdebug.remote_enable=1|on)
//            passthru('sed -i "" "s/xdebug.remote_autostart=0/xdebug.remote_autostart=1/g" ' . $iniPath . 'z-performance.ini');
            var_dump($enable);
        })->descriptions(
            'Enable or Disable xdebug on the shop container', [
                '--enable' => 'Enables xdebug',
            ]
        );
    }
}
