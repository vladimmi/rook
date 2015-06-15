<?php

namespace Rook\Config;

use Rook\Config\Exception\KeyNotFoundException;

class Config
{
    /**
     * Loaded data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Merge current config data with new data loaded via specified loader
     *
     * @param Loader $loader New data source
     * @return void
     */
    public function merge(Loader $loader)
    {
        $newData = $loader->load();
        if (is_array($newData)) {
            $this->data = array_replace_recursive($this->data, $newData);
        }
    }

    /**
     * Get value with specified key
     *
     * @param string $key Key to load data. Can be nested like 'key1.key2.key3'
     * @return mixed
     * @throws KeyNotFoundException if the whole key or some part of it does not exist
     */
    public function get($key)
    {
        $atoms = explode('.', $key);
        $result = $this->data;
        foreach ($atoms as $atom) {
            if (isset($result[$atom])) {
                $result = $result[$atom];
            } else {
                throw new KeyNotFoundException($atom, $key);
            }
        }
        return $result;
    }
}