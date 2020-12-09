<?php declare(strict_types=1);

namespace DIW\Commands;


use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class SshKeyCommand implements CommandInterface
{
    private const PUB_KEY_PATH = '~/.ssh/id_rsa.pub';

    public static function command(Application $app): void
    {
        $app->command('ssh-key', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);

            passthruCommand('pbcopy < ' . SshKeyCommand::PUB_KEY_PATH);

            $io->success('Copied ssh key to your clipboard. Use command + c to paste the content somewhere.');
        })->descriptions('Copy ssh key to your clipboard');
    }
}
