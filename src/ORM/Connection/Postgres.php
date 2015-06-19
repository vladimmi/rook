<?php

namespace Rook\ORM\Connection;

class Postgres extends Base
{
    /**
     * @var string[]
     */
    protected $preparedQueries = [];

    /**
     * Connection resource
     *
     * @var null|resource
     */
    private $connection = null;

    /**
     * Get connection resource
     *
     * @return resource
     * @throws \Exception
     */
    protected function getConnection()
    {
        if($this->connection === null) {
            //init connection on first use
            $this->connection = pg_connect($this->parameters['connection_string']);
            if($this->connection === false) {
                throw new \Exception('Cannot connect to PostgreSQL');
            }
        }

        return $this->connection;
    }

    /**
     * Executed specified query
     *
     * @param string $query
     * @param array $params
     * @return resource
     * @throws \Exception
     */
    public function query($query, $params = [])
    {
        if(!isset($this->preparedQueries[$query])) {
            $queryName = md5($query);
            $result = pg_prepare($this->getConnection(), $queryName, $query);
            if($result === false) {
                throw new \Exception('Error while trying to prepare SQL query: ' . $query);
            }
            $this->preparedQueries[$query] = $queryName;
        }

        $queryName = $this->preparedQueries[$query];
        return pg_execute($this->getConnection(), $queryName, $params);
    }

}