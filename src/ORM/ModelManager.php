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
use Rook\ORM\Metadata\Container;
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
     * Models metadata
     *
     * @var Container
     */
    protected $metadata = null;

    /**
     * Objects hydrator
     *
     * @var Hydrator
     */
    protected $hydrator = null;

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
     * Set models metadata container
     *
     * @param Container $container
     */
    public function setMetadataContainer($container)
    {
        $this->metadata = $container;
    }

    /**
     * Set object hydrator service
     *
     * @param Hydrator $hydrator
     */
    public function setHydrator($hydrator)
    {
        $this->hydrator = $hydrator;
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
            $queryProcessed = strtr($query, $this->metadata->getTableNames());
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
        if(!$this->metadata->loadMetadata($modelClass)) {
            throw new ModelMetadataException($modelClass);
        }

        $result = [];
        $response = $this->query($query, $params);

        if(pg_num_rows($response)) {
            while(($r = pg_fetch_assoc($response)) !== false) {
                $result[] = $this->hydrator->hydrate($r, $modelClass);
            }
        }

        return $result;
    }
}