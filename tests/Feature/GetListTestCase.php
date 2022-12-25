<?php

declare(strict_types=1);

namespace Feature;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Sdpgs\Gyazo\GyazoClient;

class GetListTestCase extends TestCase
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
    }

    public function testBasic(): void
    {
        $this->assertTrue(true);
    }
}
