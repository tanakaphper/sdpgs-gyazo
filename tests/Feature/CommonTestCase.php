<?php

declare(strict_types=1);

namespace Feature;

use Dotenv\Dotenv;
use Exception;
use PHPUnit\Framework\TestCase;
use Sdpgs\Gyazo\GyazoClient;
use Sdpgs\Gyazo\GyazoException;

class CommonTestCase extends TestCase
{
    /**
     * @return string
     * @throws Exception
     */
    public function testUploadImage(): string
    {
        $gyazoClient = self::getGyazoClient();

        // test upload image, while actual send http request to Gyazo.
        usleep(300000);
        $uploadImageResponse = $gyazoClient->uploadImage(
            (string)file_get_contents(__DIR__ . '/test.png'),
            'test.png'
        );
        $this->assertArrayHasKey(
            'image_id',
            $uploadImageResponse,
            'Image upload response has image_id key'
        );
        $imageId = $uploadImageResponse['image_id'];
        $this->assertArrayHasKey(
            'url',
            $uploadImageResponse,
            'Image upload response has url key'
        );
        $this->assertStringStartsWith(
            'http',
            $uploadImageResponse['url'],
            'Url value starts with http'
        );

        return $imageId;
    }

    /**
     * @depends testUploadImage
     * @param string $imageId
     * @return string
     * @throws GyazoException
     */
    public function testGetImage(string $imageId): string
    {
        $gyazoClient = self::getGyazoClient();

        // test get image, while actual send http request to Gyazo.
        usleep(300000);
        $getImageResponse = $gyazoClient->getImage($imageId);
        $this->assertArrayHasKey(
            'image_id',
            $getImageResponse,
            'Image upload response has image_id key'
        );

        return $getImageResponse['image_id'];
    }

    /**
     * @depends testGetImage
     * @param string $imageId
     * @return string
     * @throws GyazoException
     */
    public function testDeleteImage(string $imageId): string
    {
        $gyazoClient = self::getGyazoClient();

        // test delete image, while actual send http request to Gyazo.
        usleep(300000);
        $deleteImageResponse = $gyazoClient->deleteImage($imageId);
        $this->assertArrayHasKey(
            'image_id',
            $deleteImageResponse,
            'Delete image response has image_id key'
        );

        return $deleteImageResponse['image_id'];
    }

    /**
     * Test that deleted image cannot to be got.
     *
     * @depends testDeleteImage
     * @param string $imageId
     * @return void
     * @throws GyazoException
     */
    public function testNotFound(string $imageId): void
    {
        $this->expectException(GyazoException::class);
        $this->expectExceptionCode(404);

        $gyazoClient = self::getGyazoClient();
        usleep(300000);
        $gyazoClient->getImage($imageId);
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
