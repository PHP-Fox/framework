<?php

declare(strict_types=1);

namespace PHPFox;

use JustSteveKing\Config\Repository;
use PHPFox\Support\Exceptions\ConfigLoadingException;

class Application
{
    private static Application $instance;

    private Repository $config;

    private function __construct(
        private string $basePath,
        private bool $booted = false,
    ) {}

    public static function boot(string $basePath): Application
    {
        if (! isset(static::$instance)) {
            static::$instance = new static(
                basePath: $basePath,
            );
        }

        $app = static::$instance;

        // load all the things
        $app->loadConfig();

        $app->booted = true;

        return $app;
    }

    public function loadConfig(): void
    {
        $files = glob($this->basePath() . 'config/*.php');

        if (empty($files)) {
            throw new ConfigLoadingException(
                message: "Could not load config files from [$this->basePath]config/, please ensure they exist."
            );
        }

        $config = [];

        foreach ($files as $file) {
            $info = pathinfo(
                path: $file,
            );

            $config[$info['filename']] = require $file;
        }

        $this->config = Repository::build(
            items: $config,
        );
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function config(): Repository
    {
        return $this->config;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    public function run(): void
    {}
}
