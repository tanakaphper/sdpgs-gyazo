<?php

declare(strict_types=1);

namespace Sdpgs\Gyazo;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Sdpgs\Gyazo\Enums\AccessPolicyEnum;
use Sdpgs\Gyazo\Enums\GyazoEndpointUriEnum;
use Sdpgs\Gyazo\Enums\MetadataIsPublicEnum;

class GyazoClient
{
    private static Client $client;

    private function __construct(
        private readonly string $accessToken
    ) {
        if (!isset(self::$client)) {
            self::$client = new Client();
        }
    }

    /**
     * @param string $accessToken
     * @return self
     */
    public static function getInstance(
        string $accessToken
    ): self {
        return new self(
            accessToken: $accessToken
        );
    }

    /**
     * Fetch user's image list
     *
     * @see https://gyazo.com/api/docs/image
     * @return array<int, array{
     *     image_id: string,
     *     permalink_url: string|null,
     *     url: string,
     *     access_policy: string|null,
     *     metadata: array{
     *         app: mixed|null,
     *         title: mixed|null,
     *         url: mixed|null,
     *         desc: mixed|null,
     *         original_title?: mixed|null,
     *         original_url?: mixed|null
     *     },
     *     type: string|null,
     *     thumb_url: string|null,
     *     created_at: string|null
     * }>
     * @throws GyazoException
     */
    public function getList(): array
    {
        try {
            $res = self::$client->request(
                method: 'GET',
                uri: GyazoEndpointUriEnum::LIST->value,
                options: [
                    'headers' => [
                        'Authorization' => "Bearer {$this->accessToken}"
                    ]
                ]
            )
                ->getBody()
                ->getContents();
        } catch (GuzzleException $guzzleException) {
            throw new GyazoException(
                message: $guzzleException->getMessage(),
                code: $guzzleException->getCode(),
                previous: $guzzleException
            );
        }

        $responseDecoded = json_decode($res, true);
        if (!is_array($responseDecoded)) {
            throw new GyazoException();
        }

        $return = [];
        foreach ($responseDecoded as $key => $value) {
            if (!is_int($key)) {
                throw new GyazoException();
            }
            if (!is_array($value)) {
                throw new GyazoException();
            }

            $metaData = $value['metadata'];
            if (!is_array($metaData)) {
                throw new GyazoException();
            }

            $return[$key] = [
                'image_id' => strval($value['image_id'] ?? null),
                'permalink_url' => empty($value['permalink_url']) ? null : strval($value['permalink_url']),
                'url' => strval($value['url'] ?? null),
                'access_policy' => empty($value['access_policy']) ? null : strval($value['access_policy']),
                'metadata' => [
                    'app' => $metaData['app'] ?? null,
                    'title' => $metaData['title'] ?? null,
                    'url' => $metaData['url'] ?? null,
                    'desc' => $metaData['desc'] ?? null,
                    'original_title' => $metaData['original_title'] ?? null,
                    'original_url' => $metaData['original_url'] ?? null,
                ],
                'type' => $value['type'] ?? null,
                'thumb_url' => empty($value['thumb_url']) ? null : strval($value['thumb_url']),
                'created_at' => strval($value['created_at'] ?? null),
            ];
        }

        return $return;
    }

    /**
     * Upload image to Gyazo,then return metadata
     *
     * @see https://gyazo.com/api/docs/image
     * @param string $imageData
     * @param string $fileName
     * @param array{
     *     access_policy?: value-of<AccessPolicyEnum>,
     *     metadata_is_public?: value-of<MetadataIsPublicEnum>,
     *     referer_url?: string,
     *     app?: string,
     *     title?: string,
     *     desc?: string,
     *     created_at?: string,
     *     collection_id?: string
     * } $options
     * @return array{
     *     image_id: string,
     *     permalink_url: string,
     *     thumb_url: string,
     *     url: string,
     *     type: string,
     *     url: string,
     *     created_at?: string,
     *     access_policy?: mixed|null
     * }
     * @throws GyazoException
     */
    public function uploadImage(
        string $imageData,
        string $fileName,
        array $options = []
    ): array {
        try {
            $response = self::$client->request(
                'POST',
                GyazoEndpointUriEnum::UPLOAD->value,
                [
                    'headers' => [
                        'Authorization' => "Bearer {$this->accessToken}"
                    ],
                    'multipart' => [
                        [
                            'name' => 'imagedata',
                            'contents' => $imageData,
                            'filename' => $fileName
                        ],
                        ...array_map(
                            function ($key, $item) {
                                return [
                                    'name' => $key,
                                    'contents' => $item,
                                ];
                            },
                            array_keys($options),
                            array_values($options),
                        )
                    ]
                ])
                ->getBody()
                ->getContents();
        } catch (GuzzleException $guzzleException) {
            throw new GyazoException(
                message: $guzzleException->getMessage(),
                code: $guzzleException->getCode(),
                previous: $guzzleException
            );
        }

        $decodedResponse = json_decode($response, true);
        if (!is_array($decodedResponse)) {
            throw new GyazoException();
        }

        return [
            'image_id' => strval($decodedResponse['image_id'] ?? null),
            'permalink_url' => strval($decodedResponse['permalink_url'] ?? null),
            'thumb_url' => strval($decodedResponse['thumb_url'] ?? null),
            'url' => strval($decodedResponse['url'] ?? null),
            'type' => $decodedResponse['type'] ?? null,
            'access_policy' => $decodedResponse['access_policy'] ?? null,
            'created_at' => strval($decodedResponse['created_at'] ?? null),
        ];
    }

    /**
     * Delete uploaded image
     *
     * @see https://gyazo.com/api/docs/image
     * @param string $imageId
     * @return array{
     *     image_id: string,
     *     type: string
     * }
     * @throws GyazoException
     */
    public function deleteImage(string $imageId): array
    {
        try {
            $response = self::$client->request(
                'DELETE',
                GyazoEndpointUriEnum::DELETE->value . $imageId,
                [
                    'headers' => [
                        'Authorization' => "Bearer {$this->accessToken}"
                    ]
                ]
            )
                ->getBody()
                ->getContents();
        } catch (GuzzleException $guzzleException) {
            throw new GyazoException(
                message: $guzzleException->getMessage(),
                code: $guzzleException->getCode(),
                previous: $guzzleException
            );
        }

        $responseDecoded = json_decode($response, true);
        if (!is_array($responseDecoded)) {
            throw new GyazoException();
        }

        return [
            'image_id' => strval($responseDecoded['image_id'] ?? null),
            'type' => strval($responseDecoded['type'] ?? null)
        ];
    }
}
