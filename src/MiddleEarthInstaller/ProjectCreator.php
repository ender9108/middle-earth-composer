<?php

namespace EnderLab\MiddleEarthInstaller;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Script\Event;

class ProjectCreator
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

    private $composerJsonPath;

    private $composerJson;

    /**
     * @todo package installer dans framework uniquement pour l'ajout d'un nouveau package
     * @todo voir pour poser des questions
     */
    public static function createProject(Event $event)
    {
        $installer = new self($event->getIO(), $event->getComposer());
        $event->getIO()->write('<info>' . $installer->getConfig()['logo'] . '</info>');

        $event->getIO()->write('Creation directory tree');
        $installer->createDirectories();

        $event->getIO()->write('Creation configuration files');
        $installer->createConfigFiles();
    }

    public static function postCreateProject(Event $event)
    {
        $installer = new self($event->getIO(), $event->getComposer());

        $event->getIO()->write('Clean JSON définitions');

        $installer->cleanJsonDéfinitions();
    }

    public function __construct(IOInterface $io, Composer $composer)
    {
        $this->io = $io;
        $this->composer = $composer;
        $this->composerJsonPath = Factory::getComposerFile();
        $this->composerJson = new JsonFile($this->composerJsonPath);
        $this->rootPath = rtrim(realpath(dirname($this->composerJson)), '/').'/';
        $this->config = include __DIR__ . '/config/config.php';
    }

    public function createDirectories(bool $verbose = true)
    {
        foreach ($this->config['directories'] as $directory) {
            if (!is_dir($this->rootPath . $directory)) {
                if (true == mkdir($this->rootPath . $directory)) {
                    $this->io->write("\t".'- [<info>OK</info>] Create directory "<info>' . $directory . '</info>".');
                } else {
                    $this->io->write("\t".'- [<error>ERR</error>] Cannot create directory "<error>' . $directory . '</error>".');
                }
            }
        }
    }

    public function createConfigFiles(bool $verbose = true)
    {
        foreach ($this->config['template-file'] as $source => $dest) {
            if (!is_file($this->rootPath . $dest)) {
                if (true == copy(__DIR__ . '/' . $source, $this->rootPath . $dest)) {
                    $this->io->write("\t".'- [<info>OK</info>] Create file "<info>' . $dest . '</info>".');
                } else {
                    $this->io->write("\t".'- [<error>ERR</error>] Cannot create file "<error>' . $dest . '</error>".');
                }
            }
        }
    }

    public function cleanJsonDéfinitions()
    {
        $options = $this->composerJson->read();

        unset($options['authors']);

        $this->io->write("\t".'- [<info>OK</info>] Removal of unusual options.');

        $options['scripts'] = [
            "post-install-cmd" => "EnderLab\\MiddleEarth\\PackageManager\\ComposerEventManager::event",
            "post-update-cmd" => "EnderLab\\MiddleEarth\\PackageManager\\ComposerEventManager::event"
        ];

        $this->io->write("\t".'- [<info>OK</info>] Adding scripts for auto-configuration packages.');

        $this->composerJson->write($options);
    }

    public function getConfig()
    {
        return $this->config;
    }
}
