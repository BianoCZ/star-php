<?php

declare(strict_types = 1);

namespace Biano\Star;

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
use function vsprintf;
use const JSON_THROW_ON_ERROR;
use const PHP_QUERY_RFC3986;

final class Star
{

    public static function createPurchaseUrl(
        Project $project,
        Version $version,
        string $merchantId,
        string $url,
        Order $order
    ): string {
        $rid = random_bytes(16);
        $rid[6] = chr(ord($rid[6]) & 0x0f | 0x40);
        $rid[8] = chr(ord($rid[8]) & 0x3f | 0x80);

        return sprintf(
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
        );
    }

}
