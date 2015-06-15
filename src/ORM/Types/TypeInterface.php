<?php

namespace Rook\ORM\Types;

interface TypeInterface
{
    /**
     * Convert value from response to usable in PHP
     *
     * @param string $valueFromBase Source value to be decoded
     * @param mixed $params Additional parameters
     * @return mixed
     */
    public function decode($valueFromBase, $params = []);

    /**
     * Convert value from model to suitable for saving in database
     *
     * @param mixed $valueFromModel
     * @return string
     */
    public function encode($valueFromModel);
}