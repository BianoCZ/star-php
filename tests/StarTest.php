<?php

declare(strict_types = 1);

namespace Biano\Star;

use DateTimeImmutable;
use Http\Client\HttpClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use function preg_replace;

final class StarTest extends TestCase
{

    public function testCreatePurchase(): void
    {
        $request = $this->createMock(RequestInterface::class);

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory
            ->expects(self::once())
            ->method('createRequest')
            ->with(
                'GET',
                self::callback(static function (string $url): bool {
                    $url = preg_replace('/&rid=[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}/', '&rid=rid', $url);
                    self::assertSame('https://p.biano.cz/v1?merchant_id=cz123456&uid=%7C%7C%7C&url=https%3A%2F%2Fwww.referer.cz%2Findex.php%3Froute%3Dcheckout%2Fsuccess&referer=&event_type=purchase&event_data=JTdCJTIyaWQlMjIlM0ElMjIyMDA2MzgxJTIyJTJDJTIyb3JkZXJfcHJpY2UlMjIlM0ExNzE3JTJDJTIyY3VycmVuY3klMjIlM0ElMjJDWkslMjIlMkMlMjJpdGVtcyUyMiUzQSU1QiU3QiUyMmlkJTIyJTNBJTIyUE9GQTA1ODclMjIlMkMlMjJxdWFudGl0eSUyMiUzQTElMkMlMjJ1bml0X3ByaWNlJTIyJTNBMzk5JTdEJTJDJTdCJTIyaWQlMjIlM0ElMjJVQkFSMDUyMSUyMiUyQyUyMnF1YW50aXR5JTIyJTNBMSUyQyUyMnVuaXRfcHJpY2UlMjIlM0ExNTklN0QlMkMlN0IlMjJpZCUyMiUzQSUyMlVCTU8wMzE2JTIyJTJDJTIycXVhbnRpdHklMjIlM0ExJTJDJTIydW5pdF9wcmljZSUyMiUzQTExNTklN0QlNUQlMkMlMjJzb3VyY2UlMjIlM0ElMjJwaHAlMjIlMkMlMjJjdXN0b21lcl9lbWFpbCUyMiUzQSUyMnRlc3QlNDBlbWFpbC5jb20lMjIlMkMlMjJzaGlwcGluZ19kYXRlJTIyJTNBJTIyMjAyMi0wNi0wOCUyMiU3RA&rid=rid&prid=', $url);

                    return true;
                })
            )
            ->willReturn($request);

        $response = $this->createMock(ResponseInterface::class);

        $httpClient = $this->createMock(HttpClient::class);
        $httpClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $star = new Star(
            $httpClient,
            $requestFactory,
            $this->createMock(StreamFactoryInterface::class)
        );

        self::assertSame($response, $star->createPurchase(
            Project::cz(),
            Version::v1(),
            'cz123456',
            'https://www.referer.cz/index.php?route=checkout/success',
            new Order(
                '2006381',
                1717.0,
                'CZK',
                'test@email.com',
                new DateTimeImmutable('2022-06-08'),
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
        ));
    }

    public function testUpdateShippingDate(): void
    {
        $stream = $this->createMock(StreamInterface::class);

        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory
            ->expects(self::once())
            ->method('createStream')
            ->with('{"shippingDate":"2022-06-08"}')
            ->willReturn($stream);

        $request = $this->createMock(RequestInterface::class);
        $request
            ->expects(self::once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturn($request);
        $request
            ->expects(self::once())
            ->method('withBody')
            ->with($stream)
            ->willReturn($request);

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory
            ->expects(self::once())
            ->method('createRequest')
            ->with(
                'POST',
                'https://star.biano.cz/api/shipping/cz123456/2006381'
            )
            ->willReturn($request);

        $response = $this->createMock(ResponseInterface::class);

        $httpClient = $this->createMock(HttpClient::class);
        $httpClient
            ->expects(self::once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $star = new Star(
            $httpClient,
            $requestFactory,
            $streamFactory
        );

        self::assertSame($response, $star->updateShippingDate(
            Project::cz(),
            'cz123456',
            '2006381',
            new DateTimeImmutable('2022-06-08')
        ));
    }

}
