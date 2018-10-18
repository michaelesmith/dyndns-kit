<?php

namespace DynDNSKit\Tests\Integration;

use DynDNSKit\Authenticator\HttpBasicAuthenticator;
use DynDNSKit\Authenticator\User\RegexUser;
use DynDNSKit\Handler\GenericHandler;
use DynDNSKit\Processor\ProcessorInterface;
use DynDNSKit\Server;
use DynDNSKit\Tests\Common\TestCase;
use DynDNSKit\Transformer\DynDNSTransformer;
use Symfony\Component\HttpFoundation\Request;

class BasicScenarioTest extends TestCase
{
    public function testScenario1()
    {
        // We don't have a concrete processor in this project, they are all add on
        $processor = \Mockery::mock(ProcessorInterface::class);
        $processor->shouldReceive('process');

        $server = new Server([
            new GenericHandler(
                new DynDNSTransformer(),
                new HttpBasicAuthenticator([
                    new RegexUser('user1', 'pass1', '.+\.myhost\.com'),
                    new RegexUser($user = 'user2', $password = 'pass2', '.+\.myhost\.com'),
                ]),
                $processor
            )
        ]);

        $hostnames = ['test1.myhost.com', 'test2.myhost.com'];
        $ip = '192.168.1.1';
        $request = Request::create(DynDNSTransformer::PATH_INFO[0], Request::METHOD_GET, [
            DynDNSTransformer::QUERY_HOSTNAME => implode(',', $hostnames),
            DynDNSTransformer::QUERY_IP => $ip,
        ], [], [], [
            'PHP_AUTH_USER' => $user,
            'PHP_AUTH_PW' => $password,
        ]);

        // If we don't make any assertions the test is considered risky
        $this->assertNull($server->execute($request));
    }
}
