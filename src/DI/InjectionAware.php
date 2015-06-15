<?php

namespace Rook\DI;

abstract class InjectionAware
{
    public function __construct()
    {
        Container::getDefault()->injectServices($this);
    }
}