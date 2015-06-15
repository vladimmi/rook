<?php

namespace Rook\ORM\Types;

class String implements TypeInterface
{
    /**
     * Convert value from response to usable in PHP
     *
     * @param string $valueFromBase Source value to be decoded
     * @param mixed $params Additional parameters
     * @return string
     */
    public function decode($valueFromBase, $params = [])
    {
        return $valueFromBase;
    }

    /**
     * Convert value from model to suitable for saving in database
     *
     * @param string $valueFromModel
     * @return string
     */
    public function encode($valueFromModel)
    {
        return $valueFromModel;
    }
}