#!/usr/bin/env php
<?php declare(strict_types=1);

/**
 * Load correct autoloader depending on install location.
 */
if (!\file_exists(__DIR__ . '/../vendor/autoload.php')) {
    passthru(\sprintf('composer install -d %s/../', __DIR__));
}

require __DIR__ . '/../vendor/autoload.php';

use DIW\CommandLoader;
use DIW\Commands\ConfigCommand;
use Illuminate\Container\Container;
use Silly\Application;
use Symfony\Component\Yaml\Yaml;

$version = version()->getVersion();

# load config file
$globalConfig = Yaml::parseFile(__DIR__ . '/_config/' . ConfigCommand::GLOBAL_CONFIG_FILE_NAME);
$globalConfig['version'] = $version;

if (\file_exists(ConfigCommand::OVERRIDE_CONFIG_FILE_PATH) === true) {
    $overrideConfig = Yaml::parseFile(ConfigCommand::OVERRIDE_CONFIG_FILE_PATH);
    $_ENV = array_replace_recursive($globalConfig, $overrideConfig);
} else {
    $_ENV = $globalConfig;
}

$container = new Container();
Container::setInstance($container);
$app = new Application(APP_NAME, $version);

# register and load all commands
CommandLoader::load($app);

# run the application
$app->run();
