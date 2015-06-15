<?php

namespace Rook\ORM\Types;

class Json implements TypeInterface
{
    /**
     * Convert value from response to usable in PHP
     *
     * @param string $valueFromBase Source value to be decoded
     * @param mixed $params Additional parameters
     * @return array
     */
    public function decode($valueFromBase, $params = [])
    {
        return json_decode($valueFromBase, true);
    }

    /**
     * Convert value from model to suitable for saving in database
     *
     * @param array $valueFromModel
     * @return string
     */
    public function encode($valueFromModel)
    {
        return json_encode($valueFromModel);
    }

}