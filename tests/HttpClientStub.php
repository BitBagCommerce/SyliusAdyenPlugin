<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin;

use Adyen\HttpClient\ClientInterface;
use Adyen\HttpClient\CurlClient;
use Adyen\Service;

class HttpClientStub implements ClientInterface
{
    /** @var ?callable */
    private static $jsonHandler = null;

    /** @var ?callable */
    private static $postHandler = null;

    public function setJsonHandler(?callable $jsonHandler): void
    {
        self::$jsonHandler = $jsonHandler;
    }

    public function setPostHandler(?callable $postHandler): void
    {
        self::$postHandler = $postHandler;
    }

    public function requestJson(Service $service, $requestUrl, $params)
    {
        if (self::$jsonHandler !== null) {
            return call_user_func(static::$jsonHandler, $service, $requestUrl, $params);
        }

        $client = new CurlClient();

        return $client->requestJson($service, $requestUrl, $params);
    }

    public function requestPost(Service $service, $requestUrl, $params)
    {
        if (self::$postHandler !== null) {
            return call_user_func(static::$postHandler, $service, $requestUrl, $params);
        }

        $client = new CurlClient();

        return $client->requestPost($service, $requestUrl, $params);
    }
}
