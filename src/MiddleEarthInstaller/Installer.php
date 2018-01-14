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

    private $config;

    private $rootPath;

    public static function event(Event $event)
    {
        $event->getIO()->write('<info>' . $event->getName() . ' - Configuration MiddleEarth !!</info>');
        $installer = new self($event->getIO(), $event->getComposer());

        $installer->createDirectories();
        $installer->createConfigFiles();
    }

    public function __construct(IOInterface $io, Composer $composer)
    {
        $this->io = $io;
        $this->composer = $composer;
        $this->rootPath = rtrim(realpath(dirname(Factory::getComposerFile())), '/').'/';
        $this->config = include __DIR__ . '/config/config.php';
    }

    public function createDirectories(bool $verbose = true)
    {
        foreach ($this->config['directories'] as $directory) {
            if (!is_dir($this->rootPath . $directory)) {
                mkdir($this->rootPath . $directory);

                if (true === $verbose) {
                    $this->io->write('Create directory "' . $this->rootPath . $directory . '".');
                }
            }
        }
    }

    public function createConfigFiles(bool $verbose = true)
    {
        foreach ($this->config['template-file'] as $source => $dest) {
            if (!is_file($this->rootPath . $dest)) {
                copy(__DIR__ . '/' . $source, $this->rootPath . $dest);

                if (true === $verbose) {
                    $this->io->write('Create file "' . $dest . '".');
                }
            }
        }
    }
}
