<?php

namespace DynDNSKit\Transformer;

class TransformerFailedException extends TransformerException
{
    const REQUEST_METHOD = 100;

    const REQUEST_PARAMETER = 200;

    const REQUEST_PATH = 300;
}
