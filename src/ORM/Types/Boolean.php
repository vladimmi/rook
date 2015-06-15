<?php

namespace Rook\ORM\Types;

class Boolean implements TypeInterface
{
    /**
     * Convert value from response to usable in PHP
     *
     * @param string $valueFromBase Source value to be decoded
     * @param mixed $params Additional parameters
     * @return bool
     */
    public function decode($valueFromBase, $params = [])
    {
        return in_array($valueFromBase, ['t', 'true', 'y', 'yes', '1']);
    }

    /**
     * Convert value from model to suitable for saving in database
     *
     * @param bool $valueFromModel
     * @return string
     */
    public function encode($valueFromModel)
    {
        return ($valueFromModel ? 't' : 'f');
    }
}