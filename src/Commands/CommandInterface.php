<?php declare(strict_types=1);


namespace DIW\Commands;


use Silly\Application;

/**
 * @copyright 2020 dasistweb GmbH (https://www.dasistweb.de)
 */
interface CommandInterface
{
    public static function command(Application $app): void;
}
