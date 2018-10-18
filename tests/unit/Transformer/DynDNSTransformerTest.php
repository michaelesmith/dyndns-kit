<?php

namespace DynDNSKit\Tests\Unit\Transformer;

use DynDNSKit\Query;
use DynDNSKit\Tests\Common\TestCase;
use DynDNSKit\Transformer\DynDNSTransformer;
use DynDNSKit\Transformer\TransformerFailedException;
use Symfony\Component\HttpFoundation\Request;

class DynDNSTransformerTest extends TestCase
{
    public function dpTestTransform()
    {
        return [
            0 => [
                null, // $expected
                Request::create('/', Request::METHOD_GET, [
                    DynDNSTransformer::QUERY_HOSTNAME => 'myhost.com',
                    DynDNSTransformer::QUERY_IP => '192.0.0.1',
                ]), // $request
                'Bad path', // $message
                TransformerFailedException::REQUEST_PATH, // $exceptionCode
            ],
            1 => [
                null, // $expected
                Request::create(DynDNSTransformer::PATH_INFO[0]), // $request
                'Hostname must be provided in the query string', // $message
                TransformerFailedException::REQUEST_PARAMETER, // $exceptionCode
            ],
            2 => [
                new Query($ip = '192.0.0.1', $hostnames = ['myhost.com']), // $expected
                Request::create(DynDNSTransformer::PATH_INFO[0], Request::METHOD_GET, [
                    DynDNSTransformer::QUERY_HOSTNAME => implode(',', $hostnames),
                    DynDNSTransformer::QUERY_IP => $ip,
                ]), // $request
                'Both ip and a single hostname is supplied in the query string', // $message
            ],
            3 => [
                null, // $expected
                Request::create(DynDNSTransformer::PATH_INFO[0], Request::METHOD_POST, [
                    DynDNSTransformer::QUERY_HOSTNAME => implode(',', $hostnames),
                    DynDNSTransformer::QUERY_IP => $ip,
                ]), // $request
                'Only get method is allowed even if data is supplied', // $message
                TransformerFailedException::REQUEST_METHOD, // $exceptionCode
            ],
            4 => [
                new Query($ip = '192.0.0.1', $hostnames = ['myhost.com']), // $expected
                Request::create(DynDNSTransformer::PATH_INFO[0], Request::METHOD_GET, [
                    DynDNSTransformer::QUERY_HOSTNAME => implode(',', $hostnames),
                ], [], [], [
                    'REMOTE_ADDR' => $ip,
                ]), // $request
                'IP is pulled form what the client', // $message
            ],
            5 => [
                new Query($ip = '192.0.0.1', $hostnames = ['myhost.com']), // $expected
                Request::create(DynDNSTransformer::PATH_INFO[1], Request::METHOD_GET, [
                    DynDNSTransformer::QUERY_HOSTNAME => implode(',', $hostnames),
                    DynDNSTransformer::QUERY_IP => $ip,
                ]), // $request
                'Alternate path info works as expected', // $message
            ],
            6 => [
                new Query($ip = '192.0.0.1', $hostnames = ['myhost.com', 'myhost.org', 'myhost.net']), // $expected
                Request::create(DynDNSTransformer::PATH_INFO[0], Request::METHOD_GET, [
                    DynDNSTransformer::QUERY_HOSTNAME => implode(',', $hostnames),
                    DynDNSTransformer::QUERY_IP => $ip,
                ]), // $request
                'Alternate path info works as expected', // $message
            ],
        ];
    }

    /**
     * @dataProvider dpTestTransform
     */
    public function testTransform($expected, $request, $message = '', $exceptionCode = null)
    {
        $sut = new DynDNSTransformer();

        if ($exceptionCode) {
            $this->expectException(TransformerFailedException::class);
            $this->expectExceptionCode($exceptionCode);
        }

        $result = $sut->transform($request);

        $this->assertEquals($expected, $result, $message);
    }
}
