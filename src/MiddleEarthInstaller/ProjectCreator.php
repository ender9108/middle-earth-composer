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

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $rootPath;

    /**
     * @var string
     */
    private $composerJsonPath;

    /**
     * @var JsonFile
     */
    private $composerJson;

    /**
     * @todo package installer dans framework uniquement pour l'ajout d'un nouveau package
     * @todo voir pour poser des questions
     */
    public static function createProject(Event $event)
    {
        $installer = new self($event->getIO(), $event->getComposer());
        $event->getIO()->write('<info>' . $installer->getConfig()['logo'] . '</info>'."\n");

        $event->getIO()->write("\n".'<question>Creation directory tree</question>'."\n");
        $installer->createDirectories();

        $installer->askAddExempleQuestion();

        $event->getIO()->write("\n".'<question>Creation configuration files</question>'."\n");
        $installer->createConfigFiles();
    }

    public static function postCreateProject(Event $event)
    {
        $installer = new self($event->getIO(), $event->getComposer());

        $event->getIO()->write("\n".'<question>Clean JSON définitions</question>'."\n");
        $installer->cleanJsonDefinitions();

        $event->getIO()->write("\n".'<question>Clean Directories</question>'."\n");
        $installer->cleanDirectories();

        $event->getIO()->write("\n".'<question>Congratulations your project is created !</question>'."\n");
        $event->getIO()->write("\n".'- Run your application !'."\n");
        $event->getIO()->write("\t".'1. Change to the project directory');
        $event->getIO()->write("\t".'2. Execute the php -S 127.0.0.1:8080 -t ./public');
        $event->getIO()->write("\t".'3. Browse to the http://localhost:8080/');

        $event->getIO()->write("\n".'- Read the documentation at https://github.com/ender9108/middle-earth-framework/blob/master/docs/index.md'."\n");
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

    public function askAddExempleQuestion()
    {
        $query = [
            sprintf(
                "\n<question>%s</question>\n",
                'Would you like install exemple?'
            ),
            "\t[<comment>y</comment>] Yes\n",
            "\t[<comment>n</comment>] No\n",
            "\t".'Make your selection <comment>(n)</comment>: ',
        ];

        while (true) {
            $answer = $this->io->ask($query, 'n');

            switch (true) {
                case ($answer === 'y'):
                case ($answer === 'Y'):
                    $this->copyExemple();
                    $this->io->write("\n\t".'- [<info>OK</info>] Creation exemple.');
                    return;
                    break;
                case ($answer === 'n'):
                case ($answer === 'N'):
                    return;
                    break;
                default:
                    $this->io->write("\n\t".'[<error>ERR</error>] Invalid answer'."\n");
                    break;
            }
        }
    }

    private function cleanDirectories()
    {
        $directory = dirname(__FILE__).'/';
        $srcDir = $this->rootPath.'src/';

        if (! is_dir($directory)) {
            return;
        }

        $rdi = new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS);
        $rii = new \RecursiveIteratorIterator($rdi, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($rii as $filename => $fileInfo) {
            if ($fileInfo->isDir()) {
                rmdir($filename);
                continue;
            }
            unlink($filename);
        }

        rmdir($directory);
        rmdir($srcDir);

        $this->io->write("\t".'- [<info>OK</info>] Remove "<info>src/</info>".');
    }

    public function getConfig()
    {
        return $this->config;
    }

    private function copyExemple()
    {
        foreach ($this->config['exemple']['files'] as $source => $dest) {
            if (!is_file($this->rootPath . $dest)) {
                copy(__DIR__ . '/' . $source, $this->rootPath . $dest);
                /*if (true == copy(__DIR__ . '/' . $source, $this->rootPath . $dest)) {
                    $this->io->write("\t".'- [<info>OK</info>] Create file "<info>' . $dest . '</info>".');
                } else {
                    $this->io->write("\t".'- [<error>ERR</error>] Cannot create file "<error>' . $dest . '</error>".');
                }*/
            }
        }
    }
}
