<?php

namespace Rook\Config\Loader;

use Rook\Config\Loader;

class Yaml extends Base
{
    /**
     * Load config
     *
     * @return mixed Loaded and parsed config as associative array
     */
    public function load()
    {
        if(file_exists($this->configPath)) {
            return yaml_parse_file($this->configPath);
        }
        return null;
    }
}