<?php declare(strict_types=1);

namespace DIW\Commands;


use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class NgrokCommand implements CommandInterface
{
    public const TUNNEL_INSPECTION_URL = 'http://localhost:4040/inspect/http';

    public static function command(Application $app): void
    {
        $app->command('public url', function ($url, InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);

            $io->writeln('Starting ngrok. Get the public url here: ' . NgrokCommand::TUNNEL_INSPECTION_URL);

            $io->note('If you close the this (CTR + c). The tunnel will be closed as well.');
            run('open ' . NgrokCommand::TUNNEL_INSPECTION_URL);

            $command = \sprintf('%s/../../bin/ngrok http %s -host-header=rewrite ${*:2}', __DIR__, $url);
            passthru($command);
        })->descriptions(
            'Starts a tunnel with the given hostname', [
                'url' => 'Traffic Url that should be forwarded. e.g. localhost:80'
            ]
        );
    }
}
