<?php

declare(strict_types=1);

namespace Feature;

use Dotenv\Dotenv;
use Exception;
use PHPUnit\Framework\TestCase;
use Sdpgs\Gyazo\GyazoClient;

class GetListTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $dotEnv = Dotenv::createUnsafeImmutable(
            paths: __DIR__,
            names: ['.env', '.env.example']
        );
        $dotEnv->safeLoad();
    }

    /**
     * TODO
     * @return void
     */
    public function testGetList(): void
    {
        $this->assertTrue(true);
    }

    /**
     * TODO
     * @return void
     * @throws Exception
     */
    public function testUploadImage(): void
    {
        $gyazoClient = self::getGyazoClient();
        $res = $gyazoClient->uploadImage(
            file_get_contents(__DIR__ . '/test.png'),
            'test.png'
        );
        $this->assertTrue(true);
    }

    /**
     * @return GyazoClient
     * @throws Exception
     */
    private function getGyazoClient(): GyazoClient
    {
        $gyazoAccessToken = getenv('GYAZO_ACCESS_TOKEN');
        if (empty($gyazoAccessToken)) {
            throw new Exception('failed to set gyazo access token');
        }

        return GyazoClient::getInstance($gyazoAccessToken);
    }
}
