<?php declare(strict_types=1);

namespace DIW;


use DIW\Commands\CommandInterface;
use Silly\Application;
use Silly\Command\Command;
use Symfony\Component\Finder\Finder;


class CommandLoader
{
    private const COMMAND_NAME_SPACE = 'DIW\\Commands\\';

    public static function load(Application $app): void
    {
        $finder = new Finder();
        $finder->in(__DIR__ . '/Commands/')
            ->notName('CommandInterface.php')
            ->ignoreUnreadableDirs(true)
            ->ignoreDotFiles(true)
            ->name('*.php')
            ->files();

        foreach ($finder->files() as $file) {

            $class = self::COMMAND_NAME_SPACE . $file->getBasename('.php');

            /** @var CommandInterface $file */
            $reflection = new \ReflectionClass($class);
            $reflectionClass = $reflection->newInstance();

            if (!$reflectionClass instanceof CommandInterface) {
                throw new \Exception('Please use the CommandInterface for: ' . $class);
            }

            $reflectionClass::command($app);
        }
    }
}
