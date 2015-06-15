<?php

namespace Rook\ORM;

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

class ModelManager extends InjectionAware
{
    /**
     * Connection string
     *
     * @var string
     */
    private $connectionString;

    /**
     * Connection resource
     *
     * @var null|resource
     */
    private $connection = null;

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
     * Field type converters
     *
     * @var TypeInterface[]
     */
    private $fieldTypes = [];

    /**
     * @var string[]
     */
    private $preparedQueries = [];

    /**
     * Get connection resource
     *
     * @return resource
     * @throws \Exception if connection is unavailable
     */
    protected function getConnection()
    {
        if($this->connection === null) {
            //init connection on first use
            $this->connection = pg_connect($this->connectionString);
            if($this->connection === false) {
                throw new \Exception('Cannot connect to PostgreSQL');
            }
        }

        return $this->connection;
    }

    /**
     * @param string $connectionString Connection string to be used by model manager to connect PostgreSQL server
     */
    public function __construct($connectionString)
    {
        parent::__construct();

        $this->connectionString = $connectionString;
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
        if(isset($this->tableNames[$modelClass])) return true;

        $classReflection = new \ReflectionClass($modelClass);
        $classAnnotations = $this->annotations->getClassAnnotations($classReflection);
        $isModel = false;
        foreach($classAnnotations as $classAnnotation) {
            if($classAnnotation instanceof Table) {
                if(empty($classAnnotation->name)) {
                    throw new TableAnnotationException($classReflection->getName());
                }
                $this->tableNames['@' . $modelClass] = $classAnnotation->name;
                if(!empty($classAnnotation->alias)) {
                    $alias = trim($classAnnotation->alias);
                    $this->tableNames['@' . $alias] = $classAnnotation->name;
                }
                $this->metadata[$modelClass] = [];
                $isModel = true;
            }
        }

        if($isModel) {
            foreach ($classReflection->getProperties() as $propertyReflection) {
                $propertyAnnotations = $this->annotations->getPropertyAnnotations($propertyReflection);
                foreach($propertyAnnotations as $propertyAnnotation) {
                    if($propertyAnnotation instanceof Field) {
                        if(empty($propertyAnnotation->type)) {
                            throw new FieldAnnotationException($classReflection->getName() . '::' . $propertyReflection->getName());
                        }
                        $propertyReflection->setAccessible(true);
                        $this->metadata[$modelClass][$propertyReflection->getName()] = new FieldMetadata($propertyAnnotation, $propertyReflection);
                    }
                }
            }
        }

        return $isModel;
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
        if(!$this->loadMetadata($modelClass)) {
            throw new ModelMetadataException($modelClass);
        }

        $metadata = $this->metadata[$modelClass];
        /** @var Model $obj */
        $obj = new $modelClass();

        foreach($data as $key => $value) {
            if(isset($metadata[$key])) {
                $typeClass = $metadata[$key]->getAnnotation()->type;
                if(!isset($this->fieldTypes[$typeClass])) {
                    $this->fieldTypes[$typeClass] = new $typeClass();
                }
                $metadata[$key]->getReflection()->setValue($obj, $this->fieldTypes[$typeClass]->decode($value));
            } else {
                $obj->addJoinedData($key, $value);
            }
        }

        return $obj;
    }

    /**
     * Execute SQL query
     *
     * @param string $query
     * @param array $params
     * @return resource
     * @throws \Exception if SQL query cannot be prepared
     */
    public function query($query, $params = [])
    {
        if(!isset($this->preparedQueries[$query])) {
            $queryName = md5($query);
            $queryProcessed = strtr($query, $this->tableNames);
            $result = pg_prepare($this->getConnection(), $queryName, $queryProcessed);
            if($result === false) {
                throw new \Exception('Error while trying to prepare SQL query: ' . $query);
            }
            $this->preparedQueries[$query] = $queryName;
        }

        $queryName = $this->preparedQueries[$query];
        return pg_execute($this->getConnection(), $queryName, $params);
    }

    /**
     * Execute SELECT query and hydrate result records to model objects
     *
     * @param string $modelClass
     * @param string $query
     * @param array $params
     * @return array
     * @throws ModelMetadataException if model metadata not specified for class $modelClass
     * @throws \Exception if SQL query cannot be prepared
     */
    public function fetch($modelClass, $query, $params = [])
    {
        if(!$this->loadMetadata($modelClass)) {
            throw new ModelMetadataException($modelClass);
        }

        $result = [];
        $response = $this->query($query, $params);

        if(pg_num_rows($response)) {
            while(($r = pg_fetch_assoc($response)) !== false) {
                $result[] = $this->hydrate($r, $modelClass);
            }
        }

        return $result;
    }

    public function getTableNames()
    {
        return $this->tableNames;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }
}