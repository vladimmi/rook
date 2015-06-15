<?php

namespace Rook\Config\Loader;

abstract class Base
{
    /**
     * @var string
     */
    protected $configPath;

    /**
     * Create config adapter and initialize path to load later
     *
     * @param string $configPath Path to config
     */
    public function __construct($configPath)
    {
        $this->configPath = $configPath;
    }

    /**
     * Load config
     *
     * @return mixed Loaded and parsed config as associative array
     */
    abstract public function load();
}