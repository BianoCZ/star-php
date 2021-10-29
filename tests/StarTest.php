<?php

declare(strict_types = 1);

namespace Biano\Star;

use PHPUnit\Framework\TestCase;
use function base64_decode;
use function json_decode;
use function preg_match;
use function rawurldecode;
use const JSON_THROW_ON_ERROR;

final class StarTest extends TestCase
{

    public function testCreatePurchaseUrl(): void
    {
        $url = Star::createPurchaseUrl(
            Project::cz(),
            Version::v1(),
            'cz123456',
            'https://www.referer.cz/index.php?route=checkout/success',
            new Order(
                '2006381',
                1717.0,
                'CZK',
                'test@email.com',
                null,
                new Item(
                    'POFA0587',
                    1,
                    399.0
                ),
                new Item(
                    'UBAR0521',
                    1,
                    159.0
                ),
                new Item(
                    'UBMO0316',
                    1,
                    1159.0
                ),
            )
        );

        self::assertMatchesRegularExpression(
            '~^https://p\.biano\.cz/v1\?merchant_id=cz123456&uid=%7C%7C%7C&url=https%3A%2F%2Fwww\.referer\.cz%2Findex\.php%3Froute%3Dcheckout%2Fsuccess&referer=&event_type=purchase&event_data=.*&rid=[0-9a-f-]{36}&prid=~',
            $url
        );

        preg_match('~event_data=([^&]*)~', $url, $matches);
        self::assertSame(
            [
                'id' => '2006381',
                'order_price' => 1717,
                'currency' => 'CZK',
                'items' => [
                    [
                        'id' => 'POFA0587',
                        'quantity' => 1,
                        'unit_price' => 399,
                    ],
                    [
                        'id' => 'UBAR0521',
                        'quantity' => 1,
                        'unit_price' => 159,
                    ],
                    [
                        'id' => 'UBMO0316',
                        'quantity' => 1,
                        'unit_price' => 1159,
                    ],
                ],
                'source' => 'php',
                'customer_email' => 'test@email.com',
            ],
            json_decode(rawurldecode((string) base64_decode($matches[1], true)), true, 512, JSON_THROW_ON_ERROR)
        );
    }

}
