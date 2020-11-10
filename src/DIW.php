#!/usr/bin/env php
<?php declare(strict_types=1);

/**
 * Load correct autoloader depending on install location.
 */
if (\file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    require __DIR__ . '/../../../autoload.php';
}

use DIW\Commands\CmsBlockElementGenerator;
use DIW\Commands\DevCommand;
use DIW\Commands\DockerShellCommand;
use DIW\Commands\TestCommand;
use DIW\Commands\UpdateCommand;
use DIW\Commands\XdebugCommand;
use Illuminate\Container\Container;
use Silly\Application;


Container::setInstance(new Container);
$app = new Application(APP_NAME, version()->getVersion());

#@TODO: remove
TestCommand::command($app);

# initialise commands
CmsBlockElementGenerator::command($app);
DockerShellCommand::command($app);
DevCommand::command($app);
UpdateCommand::command($app);
XdebugCommand::command($app);

# run the application
$app->run();
