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


class ConfigCommand implements CommandInterface
{
    public const GLOBAL_CONFIG_FILE_NAME = 'global.config.yaml';
    public const OVERRIDE_CONFIG_FILE_NAME = 'diw-override.config.yaml';
    public const OVERRIDE_CONFIG_FILE_PATH = '/usr/local/etc/' . ConfigCommand::OVERRIDE_CONFIG_FILE_NAME;

    public static function command(Application $app): void
    {
        $app->command('config:list', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);

            #@TODO: would be nice to have the overriden value and the default value
            $io->table(['name', 'value', 'default'], ConfigCommand::getTableContent($_ENV['config'], $_ENV['globalConfig']));
        })->descriptions('Returns the current configuration');

        $app->command('config:update', function (InputInterface $input, OutputInterface $output) {
            $io = new SymfonyStyle($input, $output);

            $helper = $this->getHelperSet()->get('question'); // @phpstan-ignore-line
            $choices = ConfigCommand::getConfigKeys($_ENV['config']);
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

            if (\file_exists(ConfigCommand::OVERRIDE_CONFIG_FILE_PATH) === true) {
                $overrideConfig = (array) Yaml::parseFile(ConfigCommand::OVERRIDE_CONFIG_FILE_PATH);
            } else {
                $overrideConfig = [];
            }

            $explodedConfigKeys = \explode('.', $configConcatKey);

            # restructure our exploded config keys
            $restructuredConfig = [];
            $restructuredConfig[$explodedConfigKeys[count($explodedConfigKeys) - 1]] = $newConfigValue;
            for ($i = count($explodedConfigKeys) - 2; $i > -1; $i--) {
                $restructuredConfig[$explodedConfigKeys[$i]] = $restructuredConfig;
                unset($restructuredConfig[$explodedConfigKeys[$i + 1]]);
            }

            $yaml = Yaml::dump(array_replace_recursive($overrideConfig, $restructuredConfig), 4);
            \file_put_contents(ConfigCommand::OVERRIDE_CONFIG_FILE_PATH, $yaml);

            $io->success(\sprintf('"%s" has been successfully changed to "%s" ', $configConcatKey, $newConfigValue));
        })->descriptions('Change default configuration');
    }

    public static function getTableContent(array $config, $globalConfig, $prefix = ''): array
    {
        global $result;

        foreach ($config as $name => $item) {
            if ($prefix === '') {
                $tmpPrefix = $name;
            } else {
                $tmpPrefix = $prefix . '.' . $name;
            }

            if (\is_array($item) === true) {
                ConfigCommand::getTableContent($item, $globalConfig[$name], $tmpPrefix);
            } else {
                $result[] = [$tmpPrefix, $item, $globalConfig[$name]];
            }
        }

        return $result;
    }

    public static function getConfigKeys(array $array, string $prefix = ''): array
    {
        global $configKeys;

        foreach ($array as $name => $item) {
            if(\in_array($name, ['version', 'SHELL_VERBOSITY'], true)) {
                continue;
            }

            if ($prefix === '') {
                $tmpPrefix = $name;
            } else {
                $tmpPrefix = $prefix . '.' . $name;
            }

            if (\is_array($item) === true) {
                ConfigCommand::getConfigKeys($item, $tmpPrefix);
            } else {
                $configKeys[] = $tmpPrefix . ': ' . $item;
            }
        }

        return $configKeys;
    }
}
