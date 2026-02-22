<?php

namespace App\Core\Routing;

use stdClass;

abstract class Schema
{
    protected static string $separator;

    protected stdClass $parts;

    final public function __construct()
    {
        $this->parts = (object) [
            'controller_fqcn'   => null,
            'controller_action' => null,
            'arguments'         => null,
        ];

        self::$separator = '/';
    }

    final public function __get($name): mixed
    {
        return property_exists($this->parts, $name) ? $this->parts->$name : null;
    }

    /**
     * @return Schema
     */
    public function parse(Router $router)
    {
    }
}
