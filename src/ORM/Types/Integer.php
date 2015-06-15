<?php

namespace Rook\ORM\Types;

class Integer implements TypeInterface
{
    /**
     * Convert value from response to usable in PHP
     *
     * @param string $valueFromBase Source value to be decoded
     * @param mixed $params Additional parameters
     * @return int
     */
    public function decode($valueFromBase, $params = [])
    {
        return (int)$valueFromBase;
    }

    /**
     * Convert value from model to suitable for saving in database
     *
     * @param int $valueFromModel
     * @return string
     */
    public function encode($valueFromModel)
    {
        return (string)$valueFromModel;
    }
}