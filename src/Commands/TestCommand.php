<?php declare(strict_types=1);

namespace DIW\Commands;


use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class TestCommand implements CommandInterface
{
    public static function command(Application $app): void
    {
        $app->command('greet [name] [--yell]', function ($name, $yell, InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);

            if ($name) {
                $text = 'Hello, ' . $name;
            } else {
                $text = 'Hello';
            }

            if ($yell) {
                $text = \strtoupper($text);
            }

            $io->writeln($text);
        })->descriptions('Greet hello');
    }
}
