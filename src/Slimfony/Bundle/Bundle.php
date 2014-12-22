<?php
/**
 * Created by PhpStorm.
 * User: krzysztofkubanek
 * Date: 15.12.14
 * Time: 00:36
 */

namespace Slimfony\Bundle;


use Pimple\Container;

class Bundle {

    protected $name;
    protected $extension;
    protected $path;
    protected $container;

    /**
     * Boots the Bundle.
     */
    public function boot()
    {
        parent::boot();
        $routes = $this->container['route_collection'];
        $env = $this->container['kernel']->getEnvironment();
        $path = $this->getPath();
        $bundleRoutes = include_once $path.'Resource/config/routing.php';
        $routes->addCollection($bundleRoutes);

        $bundleServices = include_once $path.'Resource/config/services_'.$env.'.php';

        $this->container->merge($bundleServices);
    }

    /**
     * Shutdowns the Bundle.
     */
    public function shutdown()
    {
    }

    /**
     * Registers pimple container instead of Symfony component's dependency injection
     * @param Container $pimple
     */
    public function setContainer(Container $pimple) {
        $this->container = $pimple;
    }

    /**
     * Gets the Bundle namespace.
     *
     * @return string The Bundle namespace
     *
     * @api
     */
    public function getNamespace()
    {
        $class = get_class($this);

        return substr($class, 0, strrpos($class, '\\'));
    }

    /**
     * Gets the Bundle directory path.
     *
     * @return string The Bundle absolute path
     *
     * @api
     */
    public function getPath()
    {
        if (null === $this->path) {
            $reflected = new \ReflectionObject($this);
            $this->path = dirname($reflected->getFileName());
        }

        return $this->path;
    }


    /**
     * Returns the bundle parent name.
     *
     * @return string The Bundle parent name it overrides or null if no parent
     *
     * @api
     */
    public function getParent()
    {
    }


    /**
     * Returns the bundle name (the class short name).
     *
     * @return string The Bundle name
     *
     * @api
     */
    final public function getName()
    {
        if (null !== $this->name) {
            return $this->name;
        }

        $name = get_class($this);
        $pos = strrpos($name, '\\');

        return $this->name = false === $pos ? $name : substr($name, $pos + 1);
    }

    /**
     * Finds and registers Commands.
     *
     * Override this method if your bundle commands do not follow the conventions:
     *
     * * Commands are in the 'Command' sub-directory
     * * Commands extend Symfony\Component\Console\Command\Command
     *
     * @param Application $application An Application instance
     */
    public function registerCommands(Application $application)
    {
        if (!is_dir($dir = $this->getPath().'/Command')) {
            return;
        }

        $finder = new Finder();
        $finder->files()->name('*Command.php')->in($dir);

        $prefix = $this->getNamespace().'\\Command';
        foreach ($finder as $file) {
            $ns = $prefix;
            if ($relativePath = $file->getRelativePath()) {
                $ns .= '\\'.strtr($relativePath, '/', '\\');
            }
            $class = $ns.'\\'.$file->getBasename('.php');
            if ($this->container) {
                $alias = 'console.command.'.strtolower(str_replace('\\', '_', $class));
                if ($this->container->has($alias)) {
                    continue;
                }
            }
            $r = new \ReflectionClass($class);
            if ($r->isSubclassOf('Symfony\\Component\\Console\\Command\\Command') && !$r->isAbstract() && !$r->getConstructor()->getNumberOfRequiredParameters()) {
                $application->add($r->newInstance());
            }
        }
    }
} 