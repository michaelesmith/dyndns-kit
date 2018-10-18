<?php

namespace DynDNSKit\Authenticator;

use DynDNSKit\Query;
use Symfony\Component\HttpFoundation\Request;

interface AuthenticatorInterface
{
    /**
     * @param Request $request
     * @param Query $query
     * @return bool
     * @throws AuthenticatorException
     */
    public function authenticate(Request $request, Query $query): bool;
}
