<?php

namespace App;

use Psr\Log\LogLevel;

class Environment
{
    protected array $data = [];

    // Environments
    public const ENV_DEVELOPMENT = 'development';
    public const ENV_TESTING = 'testing';
    public const ENV_PRODUCTION = 'production';

    // Core settings values
    public const APP_NAME = 'APP_NAME';
    public const APP_ENV = 'APPLICATION_ENV';

    public const BASE_DIR = 'BASE_DIR';
    public const TEMP_DIR = 'TEMP_DIR';
    public const CONFIG_DIR = 'CONFIG_DIR';

    public const IS_DOCKER = 'IS_DOCKER';
    public const IS_CLI = 'IS_CLI';

    // Database
    public const DATABASE_URL = 'DATABASE_URL';

    // Central-specific items
    public const GITHUB_USERNAME = 'GITHUB_USERNAME';
    public const GITHUB_TOKEN = 'GITHUB_TOKEN';

    // Default settings
    protected array $defaults = [
        self::APP_NAME => 'AzuraCast',
        self::APP_ENV => self::ENV_PRODUCTION,

        self::IS_DOCKER => true,
        self::IS_CLI => ('cli' === PHP_SAPI),
    ];

    public function __construct(array $elements = [])
    {
        $this->data = array_merge($this->defaults, $elements);
    }

    public function getAppEnvironment(): string
    {
        return $this->data[self::APP_ENV] ?? self::ENV_PRODUCTION;
    }

    public function isProduction(): bool
    {
        return self::ENV_PRODUCTION === $this->getAppEnvironment();
    }

    public function isTesting(): bool
    {
        return self::ENV_TESTING === $this->getAppEnvironment();
    }

    public function isDevelopment(): bool
    {
        return self::ENV_DEVELOPMENT === $this->getAppEnvironment();
    }

    public function isDocker(): bool
    {
        return $this->data[self::IS_DOCKER] ?? true;
    }

    public function isCli(): bool
    {
        return $this->data[self::IS_CLI] ?? ('cli' === PHP_SAPI);
    }

    public function getAppName(): string
    {
        return $this->data[self::APP_NAME] ?? 'Application';
    }

    /**
     * @return string The base directory of the application, i.e. `/var/app/www` for Docker installations.
     */
    public function getBaseDirectory(): string
    {
        return $this->data[self::BASE_DIR];
    }

    /**
     * @return string The directory where temporary files are stored by the application, i.e. `/var/app/www_tmp`
     */
    public function getTempDirectory(): string
    {
        return $this->data[self::TEMP_DIR];
    }

    /**
     * @return string The directory where configuration files are stored by default.
     */
    public function getConfigDirectory(): string
    {
        return $this->data[self::CONFIG_DIR];
    }

    /**
     * @return string The parent directory the application is within, i.e. `/var/azuracast`.
     */
    public function getParentDirectory(): string
    {
        return dirname($this->getBaseDirectory());
    }

    public function getLogLevel(): string
    {
        return $this->isProduction()
            ? LogLevel::NOTICE
            : LogLevel::INFO;
    }

    public function getDatabaseUrl(): ?string
    {
        return $this->data[self::DATABASE_URL] ?? null;
    }

    public function getGitHubUsername(): ?string
    {
        return $this->data[self::GITHUB_USERNAME] ?? null;
    }

    public function getGitHubToken(): ?string
    {
        return $this->data[self::GITHUB_TOKEN] ?? null;
    }
}
