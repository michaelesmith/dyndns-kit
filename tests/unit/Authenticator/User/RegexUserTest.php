<?php

namespace DynDNSKit\Tests\Unit\Authenticator\User;

use DynDNSKit\Authenticator\User\RegexUser;
use DynDNSKit\Tests\Common\TestCase;

class RegexUserTest extends TestCase
{
    public function testAuthorizeHostname()
    {
        $sut = new RegexUser('user', 'pass', '.+\.myhost\.com');

        $this->assertTrue($sut->authorizeHostname('sub.myhost.com'));
        $this->assertTrue($sut->authorizeHostname('sub2.myhost.com'));
        $this->assertFalse($sut->authorizeHostname('myhost.com'));
        $this->assertFalse($sut->authorizeHostname('sub.myhost.net'));
    }

    /**
     * @depends testAuthorizeHostname
     */
    public function testAuthorizeHostnames()
    {
        $sut = new RegexUser('user', 'pass', '.+\.myhost\.com');

        $this->assertTrue($sut->authorizeHostnames(['sub.myhost.com', 'sub2.myhost.com']));
        $this->assertFalse($sut->authorizeHostnames(['sub.myhost.com', 'myhost.com']));
        $this->assertFalse($sut->authorizeHostnames(['myhost.com', 'sub.myhost.com']));
    }
}
