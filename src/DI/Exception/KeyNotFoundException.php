<?php

namespace Rook\DI\Exception;

class KeyNotFoundException extends DIException
{
    public function __construct($key)
    {
        parent::__construct(sprintf("Service '%s' not found", $key));
    }
}