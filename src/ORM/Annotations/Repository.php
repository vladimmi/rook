<?php

namespace Rook\ORM\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Repository
{
    /**
     * Served model class
     *
     * @var string
     */
    public $model;
}