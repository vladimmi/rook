<?php

namespace Rook\ORM;

use Rook\DI\Annotations\Inject;
use Rook\DI\Container;
use Rook\DI\InjectionAware;

abstract class Model extends InjectionAware
{
    /**
     * @var array
     */
    protected $joinedData = [];

    /**
     * Add joined value
     *
     * @param string $key
     * @param mixed $value
     */
    public function addJoinedData($key, $value)
    {
        $this->joinedData[$key] = $value;
    }

    /**
     * Get joined value
     *
     * @param string $key
     * @return mixed|null
     */
    public function getJoinedData($key)
    {
        return isset($this->joinedData[$key]) ? $this->joinedData[$key] : null;
    }

    /**
     * @var ModelManager|null
     */
    private static $modelManager = null;

    /**
     * Get model manager
     *
     * @return ModelManager
     * @throws \Rook\DI\Exception\ContainerNotFoundException
     * @throws \Rook\DI\Exception\KeyNotFoundException
     * @throws \Rook\DI\Exception\ServiceInstantiateException
     */
    protected static function getModelManager()
    {
        if(self::$modelManager === null) {
            self::$modelManager = Container::getDefault()->get('model_manager');
        }

        return self::$modelManager;
    }
}