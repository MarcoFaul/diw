<?php declare(strict_types=1);

namespace DIW\Commands;


use Httpful\Request;
use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class UpdateCommand implements CommandInterface
{
    private const GITHUB_LATEST_RELEASE_URL = 'https://api.github.com/repos/MarcoFaul/diw/releases/latest';

    public static function command(Application $app): void
    {
        $app->command('self-update', function (InputInterface $input, OutputInterface $output) {

            $response = Request::get(UpdateCommand::GITHUB_LATEST_RELEASE_URL)->send();

            $isLatestVersion = \version_compare(version()->getVersion(), $response->body->tag_name, '>=');

            if ($isLatestVersion === true) {
                $output->writeln('You are on the latest version: ' . $response->body->tag_name);
                return;
            }

            $helper = $this->getHelperSet()->get('question');
            $question = new ConfirmationQuestion('Wonna update?', false);

            if ($helper->ask($input, $output, $question)) {
                $output->writeln('Updating...');

                quietly('brew upgrade ' . \strtolower(APP_NAME));
            }

        })->descriptions('Update package');
    }
}