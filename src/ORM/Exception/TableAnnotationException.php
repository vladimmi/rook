<?php

namespace Rook\ORM\Exception;

class TableAnnotationException extends ORMException
{
    /**
     * @param string $modelClass Incomplete class name
     */
    public function __construct($modelClass)
    {
        parent::__construct('Incomplete table annotation for class ' . $modelClass);
    }
}