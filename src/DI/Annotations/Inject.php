<?php

namespace Rook\DI\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Inject
{
    /**
     * Service name to be injected
     *
     * @var string
     */
    public $value;
}