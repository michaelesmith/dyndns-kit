<?php

namespace DynDNSKit\Tests\Unit\Authenticator\User;

use DynDNSKit\Authenticator\User\AbstractUser;
use DynDNSKit\Tests\Common\TestCase;

class AbstractUserTest extends TestCase
{
    public function testCheckPassword()
    {
        $sut = \Mockery::mock(AbstractUser::class, ['user', 'pass'])
            ->makePartial()
        ;

        $this->assertTrue($sut->checkPassword('pass'));
        $this->assertFalse($sut->checkPassword('nope'));
    }
}
