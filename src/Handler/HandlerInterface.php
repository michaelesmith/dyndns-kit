<?php

namespace DynDNSKit\Handler;

use Symfony\Component\HttpFoundation\Request;

interface HandlerInterface
{
    const DEFERRED = 1;

    const SUCCESS = 2;

    /**
     * @param Request $request
     * @return int
     * @throws HandlerException
     */
    public function handle(Request $request): int;
}
