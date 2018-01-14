<?php
namespace EnderLab\MiddleEarthInstaller;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Script\Event;


class Installer
{
    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @var Composer
     */
    private $composer;

    private $directories = [
        'app',
        'config',
        'public',
        'public/js',
        'public/css',
        'tests',
        'tmp',
        'tmp/log',
        'tmp/cache'
    ];

    public static function event(Event $event)
    {
        $installer = new self($event->getIO(), $event->getComposer());

        $event->getIO()->write('<info>'.$event->getName().' - Configuration MiddleEarth !!</info>');
    }

    public function __construct(IOInterface $io, Composer $composer)
    {
        $this->io = $io;
        $this->composer = $composer;
    }

    public function createDirectories(bool $verbose = true)
    {
        foreach (self::$directories as $directory) {
            mkdir($rootDir . $directory);

            if (true === $verbose) {
                $io->write('Create directory "' . $rootDir . $directory . '".');
            }
        }
    }
}