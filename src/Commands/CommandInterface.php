<?php declare(strict_types=1);


namespace DIW\Commands;


use Silly\Application;


interface CommandInterface
{
    public static function command(Application $app): void;
}
