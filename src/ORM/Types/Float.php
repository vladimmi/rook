<?php

namespace Rook\ORM\Types;

class Float implements TypeInterface
{
    /**
     * Convert value from response to usable in PHP
     *
     * @param string $valueFromBase Source value to be decoded
     * @param mixed $params Additional parameters
     * @return float
     */
    public function decode($valueFromBase, $params = [])
    {
        return (float)$valueFromBase;
    }

    /**
     * Convert value from model to suitable for saving in database
     *
     * @param float $valueFromModel
     * @return string
     */
    public function encode($valueFromModel)
    {
        return (string)$valueFromModel;
    }

}