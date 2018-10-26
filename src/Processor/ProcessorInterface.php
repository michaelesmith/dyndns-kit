<?php

namespace DynDNSKit\Processor;

use DynDNSKit\Query;

interface ProcessorInterface
{
    /**
     * @param Query $query
     * @return bool
     * @throws ProcessorException
     */
    public function process(Query $query): bool;
}
