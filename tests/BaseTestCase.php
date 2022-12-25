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

        $dotEnv = Dotenv::createUnsafeImmutable(__DIR__);
        $dotEnv->safeLoad();

        $this->gyazoClient = GyazoClient::getInstance($_ENV['GYAZO_ACCESS_TOKEN']);
    }

    public function testGetList(): void
    {
        $images = $this->gyazoClient->getList();
        var_dump(count($images) . '枚の画像');
        $this->assertTrue(is_array($images));
    }
}
