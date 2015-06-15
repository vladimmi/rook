<?php

namespace Rook\ORM\Exception;

class FieldAnnotationException extends ORMException
{
    /**
     * @param string $modelProperty Incomplete property name
     */
    public function __construct($modelProperty)
    {
        parent::__construct('Field annotation is incomplete for property ' . $modelProperty);
    }
}