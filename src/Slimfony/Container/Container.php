<?php
/**
 * Created by PhpStorm.
 * User: krzysztofkubanek
 * Date: 15.12.14
 * Time: 10:58
 */

namespace Slimfony\Container;

class Container extends \Pimple\Container {
    public function merge(\Pimple\Container $container)
    {
        $keys = $container->keys();

        foreach ($keys as $key)
            $this[$key] = $container->raw($key);
    }
}