<?php

namespace Rook\ORM\Repository;

class Postgres extends Base
{
    /**
     * Execute arbitrary query
     *
     * @param string $query
     * @param array $params
     * @return mixed
     */
    protected function query($query, $params = [])
    {
        $queryProcessed = strtr($query, $this->metadata->getTableNames());
        return $this->connection->query($queryProcessed, $params);
    }

    /**
     * Execute SELECT query and hydrate results into objects
     *
     * @param string $query
     * @param array $params
     * @return array
     * @throws \Exception if repository has no model class specified
     * @throws \Rook\ORM\Exception\ModelMetadataException if model has no metadata specified
     */
    protected function fetch($query, $params = [])
    {
        $this->metadata->loadMetadata($this->getModelClass());

        $result = [];
        $response = $this->query($query, $params);

        if(pg_num_rows($response)) {
            while(($r = pg_fetch_assoc($response)) !== false) {
                $result[] = $this->hydrator->hydrate($r, $this->getModelClass());
            }
        }

        return $result;
    }

    protected function fetchOne($query, $params = [])
    {
        // TODO: Implement fetchOne() method.
    }

    /**
     * Find records with specified values
     *
     * @param array $criteria
     * @return array
     */
    public function find($criteria)
    {
        $query = 'SELECT * FROM @characters';
        $params = [];
        if(is_array($criteria)) {
            $where = [];
            $i = 1;
            foreach($criteria as $field => $value) {
                $where[] = $field . ' = $' . $i++;
                $params[] = $value;
            }
            $query .= ' WHERE ' . implode(' AND ', $where);
        }
        return $this->fetch($query, $params);
    }

    public function findOne($criteria)
    {
        // TODO: Implement findOne() method.
    }
}