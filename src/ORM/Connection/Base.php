<?php

namespace Rook\ORM\Connection;

abstract class Base
{
    protected $parameters = [];

    public function __construct($params = [])
    {
        $this->parameters = $params;
    }

    abstract public function query($query, $params = []);
}
