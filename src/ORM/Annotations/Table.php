<?php

namespace Rook\ORM\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Table
{
    /**
     * Table name
     *
     * @var string
     */
    public $name;

    /**
     * Alias to be used on queries
     *
     * @var string
     */
    public $alias;
}