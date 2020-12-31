<?php declare(strict_types=1);

namespace DIW\Commands;


use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

$result = [];
$configKeys = [];

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
class ConfigurationCommand implements CommandInterface
{
    public static function command(Application $app): void
    {
        $app->command('configuration:list', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);

            #@TODO: would be nice to have the overriden value and the default value
            $io->table(['name', 'value'], ConfigurationCommand::getTableContent($_ENV));
        })->descriptions('Returns the current configuration');

        $app->command('configuration:configure', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);

            $helper = $this->getHelperSet()->get('question'); // @phpstan-ignore-line
            $choices = ConfigurationCommand::getConfigKeys($_ENV);
            $updateConfigQuestion = new ChoiceQuestion('Select field to update', $choices, 0);
            $updateConfigQuestion->setErrorMessage('Your choice "%s" is invalid.');
            $updateConfigQuestion->setAutocompleterValues($choices);
            $selectedConfig = $helper->ask($input, $output, $updateConfigQuestion);
            [$configConcatKey, $configValue] = explode(':', $selectedConfig);

            $newConfigValueQuestion = new Question(\sprintf('Please enter a new value (Current: %s)' . PHP_EOL, $configValue), $configValue);
            $newConfigValue = \strtolower($helper->ask($input, $output, $newConfigValueQuestion));

            if ($newConfigValue === $configValue) {
                $io->note('Current and new value are equal.');

                return;
            }

            #1. get override yaml
            $overrideConfig = Yaml::parseFile(__DIR__ . '/../_config/override.config.yaml');
            $explodedConfigKeys = \explode('.', $configConcatKey);

            # restructure our exploded config keys
            $restructuredConfig = [];
            $restructuredConfig[$explodedConfigKeys[count($explodedConfigKeys) - 1]] = $newConfigValue;
            for ($i = count($explodedConfigKeys) - 2; $i > -1; $i--) {
                $restructuredConfig[$explodedConfigKeys[$i]] = $restructuredConfig;
                unset($restructuredConfig[$explodedConfigKeys[$i + 1]]);
            }

            #3 . array_replace_recursive
            $yaml = Yaml::dump(array_replace_recursive($overrideConfig, $restructuredConfig), 4);
            \file_put_contents(__DIR__ . '/../_config/override.config.yaml', $yaml);

            $io->success(\sprintf('"%s" has been successfully changed to "%s" ', $configConcatKey, $newConfigValue));

        })->descriptions('Change default configuration');
    }

    public static function getTableContent(array $array, $prefix = ''): array
    {
        global $result;

        foreach ($array as $name => $item) {
            if ($prefix === '') {
                $tmpPrefix = $name;
            } else {
                $tmpPrefix = $prefix . '.' . $name;
            }

            if (\is_array($item) === true) {
                ConfigurationCommand::getTableContent($item, $tmpPrefix);
            } else {
                $result[] = [$tmpPrefix, $item];
            }
        }

        return $result;
    }

    public static function getConfigKeys(array $array, string $prefix = ''): array
    {
        global $configKeys;

        foreach ($array as $name => $item) {
            if ($prefix === '') {
                $tmpPrefix = $name;
            } else {
                $tmpPrefix = $prefix . '.' . $name;
            }

            if (\is_array($item) === true) {
                ConfigurationCommand::getConfigKeys($item, $tmpPrefix);
            } else {
                $configKeys[] = $tmpPrefix . ': ' . $item;
            }
        }

        return $configKeys;
    }
}
