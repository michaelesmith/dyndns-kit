<?php

namespace DynDNSKit\Authenticator;

use DynDNSKit\Exception;

class AuthenticatorException extends Exception
{
    const USER_NOT_FOUND = 100;

    const BAD_CREDENTIALS = 200;

    const UNAUTHORIZED_HOSTNAME = 300;
}
