<?php

namespace Rook\ORM\Exception;

class ModelMetadataException extends ORMException
{
    /**
     * @param string $modelClass Model class without metadata
     */
    public function __construct($modelClass)
    {
        parent::__construct('Cannot find model annotations for class ' . $modelClass);
    }
}