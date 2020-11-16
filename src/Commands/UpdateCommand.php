<?php declare(strict_types=1);

namespace DIW\Commands;


use Httpful\Request;
use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class UpdateCommand implements CommandInterface
{
    private const GITHUB_LATEST_RELEASE_URL = 'https://api.github.com/repos/MarcoFaul/diw/releases/latest';

    public static function command(Application $app): void
    {
        $app->command('self-update', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);

            $response = Request::get(UpdateCommand::GITHUB_LATEST_RELEASE_URL)->send();

            $isLatestVersion = \version_compare(version()->getVersion(), $response->body->tag_name, '>=');

            if ($isLatestVersion === true) {
                $io->writeln('You are on the latest version: ' . $response->body->tag_name);
                return;
            }

            $helper = $this->getHelperSet()->get('question');
            $question = new ConfirmationQuestion('Wonna update?', false);

            if ($helper->ask($input, $output, $question)) {
                $io->writeln('Updating...');

                quietly('brew upgrade ' . \strtolower(APP_NAME));
            }

        })->descriptions(\sprintf('Update %s Utility', \strtolower(APP_NAME)));
    }
}
