<?php

namespace Rook\DI\Exception;

class ServiceInstantiateException extends DIException
{
    protected $key;

    protected $definition;

    protected $result;

    /**
     * @param string $key
     * @param mixed $definition
     * @param mixed $result
     */
    public function __construct($key, $definition, $result)
    {
        $this->key = $key;
        $this->definition = $definition;
        $this->result = $result;

        parent::__construct(sprintf("Cannot instantiate service '%s'", $key));
    }
}