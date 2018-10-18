<?php

namespace DynDNSKit\Processor;

use DynDNSKit\Query;

interface ProcessorInterface
{
    public function process(Query $query): bool;
}
