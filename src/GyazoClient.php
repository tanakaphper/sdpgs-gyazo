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
     * @param array{
     *     page?: positive-int,
     *     per_page?: int<1, 100>
     * } $options
     * @return array<int, array{
     *     image_id: string,
     *     permalink_url?: string|null,
     *     url?: string|null,
     *     access_policy?: string|null,
     *     metadata?: array{
     *         app?: mixed|null,
     *         title?: mixed|null,
     *         url?: mixed|null,
     *         desc?: mixed|null,
     *         original_title?: mixed|null,
     *         original_url?: mixed|null
     *     },
     *     type?: string|null,
     *     thumb_url?: string|null,
     *     created_at?: string|null
     * }>
     * @throws GyazoException
     */
    public function getList(array $options = []): array
    {
        try {
            $res = self::$client->request(
                method: 'GET',
                uri: GyazoEndpointUriEnum::LIST->value,
                options: [
                    'headers' => [
                        'Authorization' => "Bearer {$this->accessToken}"
                    ],
                    'query' => $options
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

            $return[$key] = $value;

            $imageId = strval($value['image_id'] ?? null);
            $return[$key]['image_id'] = $imageId;

            if (array_key_exists('permalink_urk', $value)) {
                $return[$key]['permalink_url'] = $value['permalink_url']
                    ? strval($value['permalink_url'])
                    : null;
            }

            if (array_key_exists('url', $value)) {
                $return[$key]['url'] = $value['url'] ?? null
                    ? strval($value['url'])
                    : null;
            }

            if (array_key_exists('access_policy', $value)) {
                $return[$key]['access_policy'] = is_null($value['access_policy'] ?? null)
                    ? strval($value['access_policy'])
                    : null;
            }

            if (array_key_exists('metadata', $value)) {
                if (is_array($value['metadata'])) {
                    $return[$key]['metadata'] = $value['metadata'];
                }
            }

            if (array_key_exists('type', $value)) {
                $return[$key]['type'] = $value['type']
                    ? strval($value['type'])
                    : null;
            }

            if (array_key_exists('thumb_url', $value)) {
                $return[$key]['thumb_url'] = $value['thumb_url']
                    ? strval($value['thumb_url'])
                    : null;
            }

            if (array_key_exists('created_at', $value)) {
                $return[$key]['created_at'] = $value['created_at']
                    ? strval($value['created_at'])
                    : null;
            }
        }

        return $return;
    }

    /**
     * Fetch image by image id
     *
     * @see https://gyazo.com/api/docs/image
     * @param string $imageId
     * @return array{
     *     image_id: string,
     *     type?: string|null,
     *     created_at?: string|null,
     *     permalink_url?: string|null,
     *     thumb_url?: string|null,
     *     metadata?: array{
     *         app?: mixed|null,
     *         title?: mixed|null,
     *         url?: mixed|null,
     *         desc?: mixed|null,
     *         original_title?: mixed|null,
     *         original_url?: mixed|null
     *     }|mixed,
     *     url?: string|null,
     *     access_policy?: mixed|null,
     *     ocr?: array{
     *         locale?: string|null,
     *         description?: string|null
     *     }|mixed
     * }
     * @throws GyazoException
     */
    public function getImage(string $imageId): array
    {
        try {
            $res = self::$client->request(
                method: 'GET',
                uri: GyazoEndpointUriEnum::IMAGE->value . $imageId,
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

        $return = $responseDecoded;

        $imageId = strval($responseDecoded['image_id'] ?? null);
        $return['image_id'] = $imageId;

        if (array_key_exists('type', $responseDecoded)) {
            $return['type'] = $responseDecoded['type']
                ? strval($responseDecoded['type'])
                : null;
        }

        if (array_key_exists('created_at', $responseDecoded)) {
            $return['created_at'] = $responseDecoded['created_at']
                ? strval($responseDecoded['created_at'])
                : null;
        }

        if (array_key_exists('permalink_urk', $responseDecoded)) {
            $return['permalink_url'] = $responseDecoded['permalink_url']
                ? strval($responseDecoded['permalink_url'])
                : null;
        }

        if (array_key_exists('thumb_url', $responseDecoded)) {
            $return['thumb_url'] = $responseDecoded['thumb_url']
                ? strval($responseDecoded['thumb_url'])
                : null;
        }

        if (array_key_exists('metadata', $responseDecoded)) {
            $return['metadata'] = $responseDecoded['metadata'];
        }

        if (array_key_exists('url', $responseDecoded)) {
            $return['url'] = $responseDecoded['url']
                ? strval($responseDecoded['url'])
                : null;
        }

        if (array_key_exists('access_policy', $responseDecoded)) {
            $return['access_policy'] = $responseDecoded['access_policy'];
        }

        if (array_key_exists('ocr', $responseDecoded)) {
            $return['ocr'] = $responseDecoded['ocr'];
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
     *     permalink_url?: string|null,
     *     thumb_url?: string|null,
     *     url?: string|null,
     *     type?: string|null,
     *     created_at?: string|null,
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

        $return = $decodedResponse;

        $imageId = strval($decodedResponse['image_id'] ?? null);
        $return['image_id'] = $imageId;

        if (array_key_exists('permalink_url', $decodedResponse)) {
            $return['permalink_url'] = $decodedResponse['permalink_url'] ?? null
                ? strval($decodedResponse['permalink_url'])
                : null;
        }

        if (array_key_exists('thumb_url', $decodedResponse)) {
            $return['thumb_url'] = $decodedResponse['thumb_url'] ?? null
                ? strval($decodedResponse['thumb_url'])
                : null;
        }

        if (array_key_exists('url', $decodedResponse)) {
            $return['url'] = $decodedResponse['url'] ?? null
                ? strval($decodedResponse['url'])
                : null;
        }

        if (array_key_exists('type', $decodedResponse)) {
            $return['type'] = $decodedResponse['type'] ?? null
                ? strval($decodedResponse['type'])
                : null;
        }

        if (array_key_exists('created_at', $decodedResponse)) {
            $return['created_at'] = $decodedResponse['created_at'] ?? null
                ? strval($decodedResponse['created_at'])
                : null;
        }

        if (array_key_exists('access_policy', $decodedResponse)) {
            $return['access_policy'] = $decodedResponse['created_at'] ?? null;
        }

        return $return;
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
                GyazoEndpointUriEnum::IMAGE->value . $imageId,
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

    /**
     * Get oEmbed data
     *
     * @see https://gyazo.com/api/docs/image
     * @param string $url
     * @return array{
     *     version: string,
     *     type: string,
     *     provider_name: string,
     *     provider_url: string,
     *     url: string,
     *     width: int,
     *     height: int,
     *     scale?: float
     * }
     * @throws GyazoException
     */
    public function getOEmbed(string $url): array
    {
        try {
            $response = self::$client->request(
                'GET',
                GyazoEndpointUriEnum::O_EMBED->value . '?url=' . $url,
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

        $return = [
            'version' => strval($responseDecoded['version'] ?? null),
            'type' => strval($responseDecoded['type'] ?? null),
            'provider_name' => strval($responseDecoded['provider_name'] ?? null),
            'provider_url' => strval($responseDecoded['provider_url'] ?? null),
            'url' => strval($responseDecoded['url'] ?? null),
            'width' => intval($responseDecoded['width'] ?? null),
            'height' => intval($responseDecoded['version'] ?? null),
        ];
        if ($scale = $responseDecoded['scale'] ?? null) {
            $return['scale'] = floatval($scale);
        }

        return $return;
    }
}
