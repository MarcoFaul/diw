<?php declare(strict_types=1);


namespace DIW\Components\Helper;


class Version
{
    /** @var string */
    private $version;

    public function __construct()
    {
        $this->version = \trim(\file_get_contents(__DIR__ . '/../../../version'));
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
