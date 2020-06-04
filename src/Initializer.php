<?php
namespace Slim4\Module;

use Composer\Autoload\ClassLoader;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

class Initializer
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var array
     */
    protected $initializerSettings;

    /**
     * @var AbstractModule
     */
    protected $moduleInstance;

    protected $classLoader;

    protected $moduleDir;

    public function __construct(App $app,ClassLoader $classLoader,$moduleDir)
    {
        $this->app = $app;
        $this->classLoader = $classLoader;
        $this->moduleDir = $moduleDir;
    }

    public function initModule(ServerRequestInterface $request)
    {
        $path = $request->getUri()->getPath();
        if ($path === '' || $path[0] !== '/') {
            $path = '/' . $path;
        }
        $path = rtrim($path,'/');
        $path = explode('/',$path);
        $module = ucfirst($path[1]);
        $this->classLoader->setPsr4($module."\\", $this->moduleDir.'/'.$module);
        $moduleName = '\\'.$module.'\\'.$module.'Module';
        if(class_exists($moduleName)) {
            $this->moduleInstance = new $moduleName();
            $this->dispatch();
            unset($path[1]);
            $Uri = $request->getUri()->withPath(implode('/',$path));
            $request = $request->withUri($Uri);
        }

        return $request;
    }

    /**
     * @return AbstractModule
     */
    public function getModuleInstance(): AbstractModule
    {
        return $this->moduleInstance;
    }

    public function dispatch()
    {
        $this->initDependencies();
        $this->initMiddleware();
        $this->initRoutes();
    }

    public function getModuleConfig()
    {
        return $this->moduleInstance->getModuleConfig();
    }

    public function initModuleConfig()
    {
        $container = $this->app->getContainer();

        $allSettings = $container['settings'];
        if (!isset($allSettings['modules']) or !is_array($allSettings['modules'])) {
            $allSettings['modules'] = [];
        }

        $allSettings['modules'] = array_merge_recursive($allSettings['modules'], $this->getModuleConfig());
        $container['settings'] = $allSettings;
    }

    public function initDependencies()
    {
        $container = $this->app->getContainer();
        $this->moduleInstance->initDependencies($container);
    }


    public function initMiddleware()
    {
        $this->moduleInstance->initMiddleware($this->app);
    }

    public function initRoutes()
    {
        $this->moduleInstance->initRoutes($this->app);
    }
}