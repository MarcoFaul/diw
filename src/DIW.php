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
use Illuminate\Container\Container;
use Silly\Application;
use Symfony\Component\Yaml\Yaml;

# load end file
$globalConfig = Yaml::parseFile(__DIR__ . '/_config/global.config.yaml');
$overrideConfig = Yaml::parseFile(__DIR__ . '/_config/override.config.yaml');
$_ENV = array_replace_recursive($globalConfig, $overrideConfig);

$container = new Container();
Container::setInstance($container);
$app = new Application(APP_NAME, version()->getVersion());

# register and load all commands
CommandLoader::load($app);

# run the application
$app->run();
