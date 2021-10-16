<?php declare(strict_types=1);

namespace DIW\Commands;

use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class DocCommand implements CommandInterface
{

    public static function command(Application $app): void
    {
        $app->command('docs', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);

            $io->writeln('Opening documentation');

            passthruCommand('open https://diw-tool.netlify.app/');
        })->descriptions('Opens documentation within a browser');
    }
}
