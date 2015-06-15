<?php

namespace Rook\ORM\Metadata;

use Rook\ORM\Annotations\Field as FieldAnnotation;

class Field
{
    /**
     * @var \ReflectionProperty
     */
    protected $reflection;

    /**
     * @var FieldAnnotation
     */
    protected $annotation;

    /**
     * @param FieldAnnotation $annotation
     * @param \ReflectionProperty $reflection
     */
    public function __construct($annotation, $reflection)
    {
        $this->annotation = $annotation;
        $this->reflection = $reflection;
    }

    /**
     * Get field reflection
     *
     * @return \ReflectionProperty
     */
    public function getReflection()
    {
        return $this->reflection;
    }

    /**
     * Get field annotation
     *
     * @return FieldAnnotation
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }
}