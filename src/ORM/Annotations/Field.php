<?php

namespace Rook\ORM\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Field
{
    /**
     * Field type FQCN
     *
     * @var string
     */
    public $type;
}