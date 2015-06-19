<?php

namespace Rook\ORM\Repository;

use Doctrine\Common\Annotations\AnnotationReader;
use Rook\ORM\Annotations\Repository;
use Rook\ORM\Connection\Base as BaseConnection;
use Rook\ORM\Hydrator;
use Rook\ORM\Metadata\Container;

abstract class Base
{
    /**
     * @var BaseConnection
     */
    protected $connection = null;

    /**
     * @var Container
     */
    protected $metadata = null;

    /**
     * @var Hydrator
     */
    protected $hydrator = null;

    /**
     * @var AnnotationReader
     */
    protected $annotations = null;

    /**
     * @var string
     */
    private $modelClass = null;

    /**
     * @param BaseConnection $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param Container $container
     */
    public function setMetadataContainer($container)
    {
        $this->metadata = $container;
    }

    /**
     * @param Hydrator $hydrator
     */
    public function setHydrator($hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * @param AnnotationReader $annotations
     */
    public function setAnnotations($annotations)
    {
        $this->annotations = $annotations;
    }

    /**
     * Get model class for this repository
     *
     * @return string
     * @throws \Exception if repository has no model class specified
     */
    protected function getModelClass()
    {
        if($this->modelClass === null) {
            $classReflection = new \ReflectionClass($this);
            $classAnnotations = $this->annotations->getClassAnnotations($classReflection);
            foreach($classAnnotations as $annotation) {
                if($annotation instanceof Repository) {
                    $this->modelClass = $annotation->model;
                }
            }

            if($this->modelClass === null) {
                throw new \Exception('Model class undefined for repository ' . $classReflection->getName());
            }
        }

        return $this->modelClass;
    }

    abstract protected function query($query, $params = []);

    abstract protected function fetch($query, $params = []);

    abstract protected function fetchOne($query, $params = []);

    abstract public function find($criteria);

    abstract public function findOne($criteria);
}