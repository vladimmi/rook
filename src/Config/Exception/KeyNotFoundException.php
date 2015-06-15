<?php

namespace Rook\Config\Exception;

class KeyNotFoundException extends ConfigException
{
    public function __construct($key, $fullKey)
    {
        parent::__construct(sprintf("Key '%s' not found when trying to locate '%s'", $key, $fullKey));
    }
}