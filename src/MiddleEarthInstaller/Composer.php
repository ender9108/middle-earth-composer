<?php
namespace EnderLab\MiddleEarthInstaller;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Script\Event;


class Composer
{
    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @var Composer
     */
    private $composer;

    public function __construct(IOInterface $io, Composer $composer)
    {
        $this->io = $io;
        $this->composer = $composer;
    }

    public static function event(Event $event)
    {
        $installer = new self($event->getIO(), $event->getComposer());
        $installer->io->write('<info>Configuration MiddleEarth !!</info>');
    }
}