<?php

namespace Rook\ORM;

use Doctrine\Common\Annotations\AnnotationReader;
use Rook\ORM\Annotations\Field;
use Rook\ORM\Annotations\Table;
use Rook\ORM\Exception\FieldAnnotationException;
use Rook\ORM\Exception\ModelMetadataException;
use Rook\ORM\Exception\TableAnnotationException;
use Rook\ORM\Metadata\Container;
use Rook\ORM\Metadata\Field as FieldMetadata;
use Rook\ORM\Types\TypeInterface;

class Hydrator
{
    /**
     * Annotations service
     *
     * @var AnnotationReader
     */
    private $annotations = null;

    /**
     * Models metadata
     *
     * @var Container
     */
    private $metadata = null;

    /**
     * Field type converters
     *
     * @var TypeInterface[]
     */
    private $fieldTypes = [];

    /**
     * Set annotations service
     *
     * @param AnnotationReader $annotations
     */
    public function setAnnotations($annotations)
    {
        $this->annotations = $annotations;
    }

    /**
     * Set models metadata container
     *
     * @param Container $container
     */
    public function setMetadataContainer($container)
    {
        $this->metadata = $container;
    }

    /**
     * Hydrate data array to model object
     *
     * @param array $data
     * @param string $modelClass
     * @return Model
     * @throws FieldAnnotationException if model field annotation is incomplete
     * @throws ModelMetadataException if model metadata not specified
     * @throws TableAnnotationException if model table annotation is incomplete
     */
    public function hydrate($data, $modelClass)
    {
        if(!$this->metadata->loadMetadata($modelClass)) {
            throw new ModelMetadataException($modelClass);
        }

        $fields = $this->metadata->getModelFields($modelClass);

        /** @var Model $obj */
        $obj = new $modelClass();

        foreach($data as $key => $value) {
            if(isset($fields[$key])) {
                $typeClass = $fields[$key]->getAnnotation()->type;
                if(!isset($this->fieldTypes[$typeClass])) {
                    $this->fieldTypes[$typeClass] = new $typeClass();
                }
                $fields[$key]->getReflection()->setValue($obj, $this->fieldTypes[$typeClass]->decode($value));
            } else {
                $obj->addJoinedData($key, $value);
            }
        }

        return $obj;
    }
}