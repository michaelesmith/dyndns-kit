<?php

namespace DynDNSKit\Transformer;

use DynDNSKit\Query;
use Symfony\Component\HttpFoundation\Request;

interface TransformerInterface
{
    /**
     * @param Request $request
     * @return Query|null
     * @throws TransformerFailedException|TransformerException
     */
    public function transform(Request $request): ?Query;
}
