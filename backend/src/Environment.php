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
    public const APP_ENV = 'APPLICATION_ENV';

    // Database
    public const DATABASE_URL = 'DATABASE_URL';

    // Auth0
    public const AUTH0_CLIENT_ID = 'AUTH0_CLIENT_ID';
    public const AUTH0_DOMAIN = 'AUTH0_DOMAIN';
    public const AUTH0_CLIENT_SECRET = 'AUTH0_CLIENT_SECRET';
    public const AUTH0_COOKIE_SECRET = 'AUTH0_COOKIE_SECRET';

    // Default settings
    public function __construct(array $elements = [])
    {
        $this->data = $elements;
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

    public function isCli(): bool
    {
        return ('cli' === PHP_SAPI);
    }

    /**
     * @return string The base directory of the application, i.e. `/var/app/www` for Docker installations.
     */
    public function getBaseDirectory(): string
    {
        return dirname(__DIR__, 2);
    }

    /**
     * @return string The parent directory the application is within, i.e. `/var/app`.
     */
    public function getParentDirectory(): string
    {
        return dirname($this->getBaseDirectory());
    }

    /**
     * @return string The directory where temporary files are stored by the application, i.e. `/var/app/www_tmp`
     */
    public function getTempDirectory(): string
    {
        return $this->getParentDirectory() . '/www_tmp';
    }

    /**
     * @return string The directory where configuration files are stored by default.
     */
    public function getConfigDirectory(): string
    {
        return $this->getBaseDirectory() . '/backend/config';
    }

    public function getLogLevel(): string
    {
        return $this->isProduction()
            ? LogLevel::NOTICE
            : LogLevel::INFO;
    }

    public function getDatabaseUrl(): string
    {
        return $this->data[self::DATABASE_URL] ?? throw new \RuntimeException('No Database URL specified!');
    }

    public function getAuth0Info(): array
    {
        return [
            'domain' => $this->data[self::AUTH0_DOMAIN] ?? null,
            'clientId' => $this->data[self::AUTH0_CLIENT_ID] ?? null,
            'clientSecret' => $this->data[self::AUTH0_CLIENT_SECRET] ?? null,
            'cookieSecret' => $this->data[self::AUTH0_COOKIE_SECRET] ?? null,
        ];
    }
}
