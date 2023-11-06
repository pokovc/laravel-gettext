<?php

namespace pokovc\LaravelGettext\Commands;

use Illuminate\Console\Command;
use pokovc\LaravelGettext\FileSystem;
use pokovc\LaravelGettext\Config\ConfigManager;

class BaseCommand extends Command
{
    /**
     * Filesystem helper.
     *
     * @var \pokovc\LaravelGettext\FileSystem
     */
    protected $fileSystem;

    /**
     * Package configuration data.
     *
     * @var array
     */
    protected $configuration;

    /**
     * Prepares the package environment for gettext commands.
     *
     */
    protected function prepare()
    {
        $configManager = ConfigManager::create();

        $this->fileSystem = new FileSystem(
            $configManager->get(),
            app_path(),
            storage_path()
        );

        $this->configuration = $configManager->get();
    }
}
