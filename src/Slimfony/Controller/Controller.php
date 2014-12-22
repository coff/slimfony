<?php
/**
 * Created by PhpStorm.
 * User: krzysztofkubanek
 * Date: 27.11.14
 * Time: 00:26
 */

namespace Slimfony\Controller;


use Symfony\Component\EventDispatcher\EventDispatcher;

class Controller {

    private $container;
    private $dispatcher;

    public function __construct()
    {

    }

    /**
     * Returns service container;
     * @return mixed
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * Returns event dispatcher
     * @return EventDispatcher
     */
    public function getDispatcher() {
        return $this->dispatcher;
    }
}