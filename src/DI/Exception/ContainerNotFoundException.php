<?php

namespace Rook\DI\Exception;

class ContainerNotFoundException extends DIException
{
    public function __construct()
    {
        parent::__construct('Default DI container was not instantiated');
    }
}