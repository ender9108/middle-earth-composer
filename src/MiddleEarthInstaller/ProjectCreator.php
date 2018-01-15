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

        $installer->cleanJsonDefinitions();

        $event->getIO()->write('Clean Directories');

        $installer->cleanDirectories();
    }

    public function __construct(IOInterface $io, Composer $composer)
    {
        $this->io = $io;
        $this->composer = $composer;
        $this->composerJsonPath = Factory::getComposerFile();
        $this->composerJson = new JsonFile($this->composerJsonPath);
        $this->rootPath = rtrim(realpath(dirname($this->composerJsonPath)), '/').'/';
        $this->config = include __DIR__ . '/config/config.php';
    }

    public function createDirectories()
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

    public function createConfigFiles()
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

    public function cleanJsonDefinitions()
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

    private function cleanDirectories()
    {
        $directory = dirname(__FILE__).'/';

        if (! is_dir($directory)) {
            return;
        }

        $rdi = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);
        $rii = new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($rii as $filename => $fileInfo) {
            if ($fileInfo->isDir()) {
                rmdir($filename);
                continue;
            }
            unlink($filename);
        }

        rmdir($directory);
    }

    public function getConfig()
    {
        return $this->config;
    }
}
