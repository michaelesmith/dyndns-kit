<?php

namespace DynDNSKit\Tests\Unit\Authenticator;

use DynDNSKit\Authenticator\AuthenticatorException;
use DynDNSKit\Authenticator\HttpBasicAuthenticator;
use DynDNSKit\Authenticator\User\UserInterface;
use DynDNSKit\Query;
use DynDNSKit\Tests\Common\TestCase;
use Symfony\Component\HttpFoundation\Request;

class HttpBasicAuthenticatorTest extends TestCase
{
    public function dpTestAuthenticate()
    {
        return [
            0 => [
                [
                    $this->mockUser($user = 'user1', $password = 'pass1', true, true),
                ], // $users
                Request::create('/', Request::METHOD_GET, [], [], [], [
                    'PHP_AUTH_USER' => $user,
                    'PHP_AUTH_PW' => $password,
                ]), //$request
                new Query('127.0.0.1', ['myhost.com']), // $query
            ],
            1 => [
                [
                    $this->mockUser('user1', 'pass1', false, false),
                    $this->mockUser($user = 'user2', $password = 'pass2', true, true),
                ], // $users
                Request::create('/', Request::METHOD_GET, [], [], [], [
                    'PHP_AUTH_USER' => $user,
                    'PHP_AUTH_PW' => $password,
                ]), //$request
                new Query('127.0.0.1', ['myhost.com']), // $query
            ],
            2 => [
                [
                    $this->mockUser('user1', 'pass1', false, false),
                    $this->mockUser('user2', $password = 'pass2', true, true),
                ], // $users
                Request::create('/', Request::METHOD_GET, [], [], [], [
                    'PHP_AUTH_USER' => 'user3',
                    'PHP_AUTH_PW' => $password,
                ]), //$request
                new Query('127.0.0.1', ['myhost.com']), // $query
                'User not found', // $message
                AuthenticatorException::USER_NOT_FOUND, // $exceptionCode
            ],
            3 => [
                [
                    $this->mockUser($user = 'user1', $password = 'pass1', false, true),
                ], // $users
                Request::create('/', Request::METHOD_GET, [], [], [], [
                    'PHP_AUTH_USER' => $user,
                    'PHP_AUTH_PW' => $password,
                ]), //$request
                new Query('127.0.0.1', ['myhost.com']), // $query
                'Password check failed', // $message
                AuthenticatorException::BAD_CREDENTIALS, // $exceptionCode
            ],
            4 => [
                [
                    $this->mockUser($user = 'user1', $password = 'pass1', false, true),
                    $this->mockUser('user2', 'pass2', true, true),
                ], // $users
                Request::create('/', Request::METHOD_GET, [], [], [], [
                    'PHP_AUTH_USER' => $user,
                    'PHP_AUTH_PW' => $password,
                ]), //$request
                new Query('127.0.0.1', ['myhost.com']), // $query
                'Only one user password is checked', // $message
                AuthenticatorException::BAD_CREDENTIALS, // $exceptionCode
            ],
            5 => [
                [
                    $this->mockUser($user = 'user1', $password = 'pass1', true, false),
                ], // $users
                Request::create('/', Request::METHOD_GET, [], [], [], [
                    'PHP_AUTH_USER' => $user,
                    'PHP_AUTH_PW' => $password,
                ]), //$request
                new Query('127.0.0.1', ['myhost.com']), // $query
                'User is not authorized for the hostnames', // $message
                AuthenticatorException::UNAUTHORIZED_HOSTNAME, // $exceptionCode
            ],
            6 => [
                [
                    $this->mockUser('User1', 'pass1', false, true),
                    $this->mockUser($user = 'user1', $password = 'pass1', true, true),
                ], // $users
                Request::create('/', Request::METHOD_GET, [], [], [], [
                    'PHP_AUTH_USER' => $user,
                    'PHP_AUTH_PW' => $password,
                ]), //$request
                new Query('127.0.0.1', ['myhost.com']), // $query
                'Usernames should be case sensitive', // $message
            ],
        ];
    }

    private function mockUser($username, $password, $checkPassword, $authorizeHostnames)
    {
        $mock = \Mockery::mock(UserInterface::class);
        $mock->shouldReceive('getUsername')->andReturn($username);
        $mock->shouldReceive('checkPassword')->with($password)->andReturn($checkPassword);
        $mock->shouldReceive('authorizeHostnames')->andReturn($authorizeHostnames);

        return $mock;
    }

    /**
     * @dataProvider dpTestAuthenticate
     */
    public function testAuthenticate($users, $request, $query, $message = '', $exceptionCode = null)
    {
        $sut = new HttpBasicAuthenticator($users);

        if ($exceptionCode) {
            $this->expectException(AuthenticatorException::class);
            $this->expectExceptionCode($exceptionCode);
        }

        $this->assertTrue($sut->authenticate($request, $query), $message);
    }
}
