<?php

declare(strict_types = 1);

namespace Biano\Star;

use DateTimeImmutable;
use Http\Client\HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use function base64_encode;
use function bin2hex;
use function chr;
use function http_build_query;
use function json_encode;
use function ord;
use function random_bytes;
use function rawurlencode;
use function rtrim;
use function sprintf;
use function str_split;
use function urlencode;
use function vsprintf;
use const JSON_THROW_ON_ERROR;
use const PHP_QUERY_RFC3986;

final class Star
{

    private HttpClient $httpClient;

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    public function __construct(
        HttpClient $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    public function createPurchase(
        Project $project,
        Version $version,
        string $merchantId,
        string $url,
        Order $order
    ): ResponseInterface {
        $rid = random_bytes(16);
        $rid[6] = chr(ord($rid[6]) & 0x0f | 0x40);
        $rid[8] = chr(ord($rid[8]) & 0x3f | 0x80);

        return $this->httpClient->sendRequest($this->requestFactory->createRequest(
            'GET',
            sprintf(
                'https://p.%s/%s?%s',
                $project->getProject(),
                $version->getVersion(),
                http_build_query([
                    'merchant_id' => $merchantId,
                    'uid' => '|||',
                    'url' => $url,
                    'referer' => '',
                    'event_type' => 'purchase',
                    'event_data' => rtrim(base64_encode(rawurlencode(json_encode($order, JSON_THROW_ON_ERROR))), '='),
                    'rid' => vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($rid), 4)),
                    'prid' => '',
                ], '', '&', PHP_QUERY_RFC3986)
            )
        ));
    }

    public function updateShippingDate(
        Project $project,
        string $merchantId,
        string $orderId,
        DateTimeImmutable $shippingDate
    ): ResponseInterface
    {
        return $this->httpClient->sendRequest($this->requestFactory->createRequest(
            'POST',
            sprintf(
                'https://star.%s/api/shipping/%s/%s',
                $project->getProject(),
                urlencode($merchantId),
                urlencode($orderId)
            )
        )
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream(json_encode([
                'shippingDate' => $shippingDate->format('Y-m-d'),
            ], JSON_THROW_ON_ERROR)))
        );

    }

}
