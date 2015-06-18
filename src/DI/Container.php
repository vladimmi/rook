<?php

namespace Rook\DI;

use Doctrine\Common\Annotations\AnnotationReader;
use Rook\Config\Config;
use Rook\DI\Annotations\Inject;
use Rook\DI\Exception\ContainerNotFoundException;
use Rook\DI\Exception\ServiceInstantiateException;
use Rook\DI\Exception\KeyNotFoundException;

class Container
{
    protected $services = [];

    protected $serviceNameConfig = 'config';

    protected function preprocessArgument($arg)
    {
        if(is_string($arg)) {
            switch($arg{0}) {
                case '@':
                    //get service
                    return $this->get(substr($arg, 1));
                case '%':
                    //get parameter
                    return $this->get($this->serviceNameConfig)->get(substr($arg, 1));
                    break;
            }
        }

        return $arg;
    }

    protected function preprocessArguments($args)
    {
        $result = [];
        foreach($args as $arg) {
            $result[] = $this->preprocessArgument($arg);
        }
        return $result;
    }

    /**
     * Instantiate object from closure
     *
     * @param \Closure $closure
     * @return mixed
     */
    protected function createFromClosure($closure)
    {
        return $closure();
    }

    /**
     * Instantiate object from config array
     *
     * @param mixed $config Service configuration
     * Possible config keys:
     * class - FQCN for service object
     * construct - array of constructor parameters
     * calls - needed method calls where key is method name and value is parameters array
     * multicalls - needed multiple method calls where key is method name and value has array or parameters arrays
     * @return mixed
     */
    protected function createFromConfig($config)
    {
        if(!isset($config['class'])) {
            throw new ServiceInstantiateException(null, $config, null);
        }

        $classReflection = new \ReflectionClass($config['class']);

        //instantiate class object
        $service = null;
        if(isset($config['construct']) && is_array($config['construct'])) {
            $service = $classReflection->newInstanceArgs($this->preprocessArguments($config['construct']));
        } else {
            $service = $classReflection->newInstanceArgs([]);
        }

        //make single calls
        if(isset($config['calls']) && is_array($config['calls'])) {
            foreach($config['calls'] as $methodName => $args) {
                call_user_func_array([$service, $methodName], $this->preprocessArguments($args));
            }
        }

        //make multiple calls
        if(isset($config['multicalls']) && is_array($config['multicalls'])) {
            foreach($config['calls'] as $methodName => $arguments) {
                foreach($arguments as $args) {
                    call_user_func_array([$service, $methodName], $this->preprocessArguments($args));
                }
            }
        }

        return $service;
    }

    public function __construct($defaultDI = true)
    {
        if ($defaultDI) {
            self::$defaultDi = $this;
        }
    }

    /**
     * Register new service
     *
     * @param string $key New service key
     * @param mixed $definition Service definition. Can be object, closure or config array
     * @return void
     */
    public function set($key, $definition)
    {
        $this->services[$key] = $definition;
    }

    /**
     * Register services from loaded config
     *
     * @param string $serviceName Config service name to use
     * @param string $rootPath Config key to load service definitions from
     * @return void
     * @throws KeyNotFoundException if specified service not found
     * @throws ServiceInstantiateException if specified service cannot be instantiated
     * @throws \Rook\Config\Exception\KeyNotFoundException if specified config path not found
     */
    public function setFromConfig($serviceName = 'config', $rootPath = 'services')
    {
        $this->serviceNameConfig = $serviceName;

        /** @var Config $config */
        $config = $this->get($serviceName);
        $definitions = $config->get($rootPath);

        if (is_array($definitions)) {
            foreach($definitions as $key => $definition) {
                $this->set($key, $definition);
            }
        }
    }

    /**
     * Get service object with specified key
     *
     * @param string $key
     * @return object
     * @throws KeyNotFoundException when specified key does not exist
     * @throws ServiceInstantiateException when service object cannot be created
     */
    public function get($key)
    {
        if (!isset($this->services[$key])) {
            throw new KeyNotFoundException($key);
        }

        $service = $this->services[$key];

        if ($service instanceof \Closure) {
            $service = $this->createFromClosure($service);
        } elseif (is_array($service)) {
            $service = $this->createFromConfig($service);
        }

        if (is_object($service)) {
            $this->services[$key] = $service;
            return $service;
        } else {
            throw new ServiceInstantiateException($key, $this->services[$key], $service);
        }
    }

    /**
     * Check if service with specified key is registered
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->services[$key]);
    }

    public function injectServices($obj)
    {
        if (!$this->has('annotations')) {
            //do nothing if annotations service is unavailable
            return;
        }

        /** @var AnnotationReader $annReader */
        $annReader = $this->get('annotations');

        $reflectionClass = new \ReflectionClass($obj);
        $props = $reflectionClass->getProperties();
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $annotations = $annReader->getPropertyAnnotations($reflectionProperty);
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Inject) {
                    if (!$reflectionProperty->isPublic()) {
                        $reflectionProperty->setAccessible(true);
                    }
                    $reflectionProperty->setValue($obj, $this->get($annotation->value));
                }
            }
        }
    }

    /**
     * Default DI container
     *
     * @var Container|null
     */
    protected static $defaultDi = null;

    /**
     * Get default DI container
     *
     * @return Container
     * @throws ContainerNotFoundException if default container was not created
     */
    public static function getDefault()
    {
        if (self::$defaultDi === null) {
            throw new ContainerNotFoundException();
        }

        return self::$defaultDi;
    }
}