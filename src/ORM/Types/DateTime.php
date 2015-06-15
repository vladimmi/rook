<?php

namespace Rook\ORM\Types;

class DateTime implements TypeInterface
{
    /**
     * Convert value from response to usable in PHP
     *
     * @param string $valueFromBase Source value to be decoded
     * @param mixed $params Additional parameters
     * @return \DateTime
     */
    public function decode($valueFromBase, $params = [])
    {
        if($valueFromBase === null) {
            return null;
        }
        return new \DateTime($valueFromBase, new \DateTimeZone('UTC'));
    }

    /**
     * Convert value from model to suitable for saving in database
     *
     * @param \DateTime $valueFromModel
     * @return string
     */
    public function encode($valueFromModel)
    {
        return $valueFromModel->getTimestamp();
    }
}