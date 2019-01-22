<?php
/**
 * Created by XZ Software.
 * Smart code for smart wallet
 * http://xzsoftware.pl
 * User adrianmodliszewski
 * Date: 22/01/2019
 * Time: 16:48
 */

namespace App\Services;
use GuzzleHttp\Client;

class ApiService
{
    /** @var string */
    private $appKey;
    /** @var string */
    private $appSecret;
    /** @var Client */
    private $httpClient;

    /**
     * @param string $appKey
     * @param string $appSecret
     * @param Client $guzzle
     */
    public function __construct($appKey, $appSecret, Client $guzzle)
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->httpClient = $guzzle;
    }

    /**
     * Returns user specific 24h authorization key or false on error
     * @param string $userLogin
     * @param string $userKey
     * @return string|bool
     */
    public function getUserSecret($userLogin, $userKey)
    {
        if (empty($userLogin) || empty($userKey)) {
            return false;
        }

        $url = 'https://a.wykop.pl/user/login/appkey,' . $this->appKey;
        $postData = [
            'accountkey' => $userKey,
            'login' => $userLogin
        ];

        $response = $this->httpClient
            ->post(
                $url,
                [
                    'headers' => [
                        'apisign' => $this->generateSigningKey($url, $postData),
                    ],
                    'body' => $postData
                ]
            );

        if ($response->getStatusCode() != 200 || !isset($response->json()['userkey'])) {
            return false;
        }

        return $response->json()['userkey'];
    }

    /**
     * @param string $url
     * @param array $postData
     * @return string
     */
    private function generateSigningKey($url, array $postData)
    {
        ksort($postData);

        return md5($this->appSecret . $url . implode(',', array_values($postData)));
    }
}
