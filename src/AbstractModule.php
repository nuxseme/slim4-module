<?php

namespace Slim4\Module;

use Composer\Autoload\ClassLoader;
use Psr\Container\ContainerInterface;
use Slim\App;

abstract class AbstractModule
{

    public function getModuleConfig()
    {
        return [];
    }

     public function initClassLoader(ClassLoader $classLoader){}

    /**
     * Set class maps for class loader to autoload classes for this module
     * @param ContainerInterface $container
     * @return void
     */
    public function initDependencies(ContainerInterface $container){}


    /**
     * Initiate app middleware, route middleware should go in load() with routes
     * @param App $app
     * @return void
     */
    public function initMiddleware(App $app){}


    /**
     * Load is run last, when config, dependencies, etc have been initiated
     * Routes ought to go here
     * @param App $app
     * @return void
     */
    public function initRoutes(App $app){}

}
