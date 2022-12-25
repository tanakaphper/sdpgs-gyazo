<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Sdpgs\Gyazo\GyazoClient;

class BaseTestCase extends TestCase
{
    private readonly GyazoClient $gyazoClient;

    protected function setUp(): void
    {
        parent::setUp();

        $dotEnv = Dotenv::createUnsafeImmutable(
            paths: __DIR__,
            names: ['.env', '.env.example']
        );
        $dotEnv->safeLoad();

        $this->gyazoClient = GyazoClient::getInstance(getenv('GYAZO_ACCESS_TOKEN'));
    }
}
