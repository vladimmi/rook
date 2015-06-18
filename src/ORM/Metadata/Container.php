<?php

namespace Rook\ORM\Metadata;

use Doctrine\Common\Annotations\AnnotationReader;
use Rook\DI\Annotations\Inject;
use Rook\DI\InjectionAware;
use Rook\ORM\Annotations\Field;
use Rook\ORM\Annotations\Table;
use Rook\ORM\Exception\FieldAnnotationException;
use Rook\ORM\Exception\ModelMetadataException;
use Rook\ORM\Exception\TableAnnotationException;
use Rook\ORM\Metadata\Field as FieldMetadata;
use Rook\ORM\Types\TypeInterface;

class Container extends InjectionAware
{
    /**
     * Annotations service
     *
     * @var AnnotationReader
     * @Inject("annotations")
     */
    private $annotations = null;

    /**
     * Table names for every loaded model class
     *
     * @var string[]
     */
    private $tableNames = [];

    /**
     * Loaded metadata for models properties
     *
     * @var FieldMetadata[][]
     */
    private $metadata = [];

    /**
     * Check if specified class metadata is loaded
     *
     * @param string $modelClass
     * @return bool
     */
    public function isLoaded($modelClass)
    {
        return isset($this->tableNames[$modelClass]);
    }

    /**
     * Get table name for specified model class or alias
     *
     * @param string $alias Model FQCN or alias
     * @return string
     * @throws TableAnnotationException if class metadata cannot be found
     */
    public function getTableName($alias)
    {
        if(!isset($this->tableNames[$alias])) {
            throw new TableAnnotationException($alias);
        }
        return $this->tableNames[$alias];
    }

    /**
     * Get associated table names
     *
     * @return string[]
     */
    public function getTableNames()
    {
        return $this->tableNames;
    }

    /**
     * Get model fields metadata
     *
     * @param string $modelClass
     * @return \Rook\ORM\Metadata\Field[]
     * @throws TableAnnotationException if class metadata cannot be found
     */
    public function getModelFields($modelClass)
    {
        if(!$this->loadMetadata($modelClass)) {
            throw new TableAnnotationException($modelClass);
        }
        return $this->metadata[$modelClass];
    }

    /**
     * Load model metadata
     *
     * @param string $modelClass
     * @return bool True if specified class is model
     * @throws TableAnnotationException if table annotation is incomplete
     * @throws FieldAnnotationException if field annotation is incomplete
     */
    public function loadMetadata($modelClass)
    {
        if($this->isLoaded($modelClass)) return true;

        $classReflection = new \ReflectionClass($modelClass);
        $classAnnotations = $this->annotations->getClassAnnotations($classReflection);
        $isModel = false;
        foreach ($classAnnotations as $classAnnotation) {
            if ($classAnnotation instanceof Table) {
                if (empty($classAnnotation->name)) {
                    throw new TableAnnotationException($classReflection->getName());
                }
                $this->tableNames['@' . $modelClass] = $classAnnotation->name;
                if (!empty($classAnnotation->alias)) {
                    $alias = trim($classAnnotation->alias);
                    $this->tableNames['@' . $alias] = $classAnnotation->name;
                }
                $this->metadata[$modelClass] = [];
                $isModel = true;
            }
        }

        if ($isModel) {
            foreach ($classReflection->getProperties() as $propertyReflection) {
                $propertyAnnotations = $this->annotations->getPropertyAnnotations($propertyReflection);
                foreach ($propertyAnnotations as $propertyAnnotation) {
                    if ($propertyAnnotation instanceof Field) {
                        if (empty($propertyAnnotation->type)) {
                            throw new FieldAnnotationException($classReflection->getName() . '::' . $propertyReflection->getName());
                        }
                        $propertyReflection->setAccessible(true);
                        $this->metadata[$modelClass][$propertyReflection->getName()] = new FieldMetadata($propertyAnnotation,
                            $propertyReflection);
                    }
                }
            }
        }

        return $isModel;
    }
}