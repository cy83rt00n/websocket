<?php

namespace App\Core\Routing\Schemas;

use App\Core\Routing\Schema;
use App\Core\Routing\Router;

class ControllerActionNamedParams extends Schema
{
    private string $controllers_namespace = '\App\Controllers\\';
    private string $controller_action_suffix = 'Action';
    private string $fallsback_controller_fqcn = 'App\Controllers\Fallback';
    private string $fallback_controller_action = 'indexAction';

    public function parse(Router $router): ControllerActionNamedParams
    {
        $parts = explode(self::$separator, ltrim($router->path, '/'));
        $controller_fqcn = $this->controllers_namespace . ucfirst(array_shift($parts));
        $isClass = class_exists($controller_fqcn);
        $this->parts->controller_fqcn = $isClass
            ? $controller_fqcn
            : $this->fallsback_controller_fqcn;
        $this->parts->controller_action = $isClass
            ? array_shift($parts) . $this->controller_action_suffix
            : $this->fallback_controller_action;
        $this->parts->arguments = [];
        foreach ($parts as $idx => $param) {
            if ($idx % 2 > 0) {
                continue;
            }
            $this->parts->arguments[$param] = $parts[$idx + 1];
        }
        return $this;
    }
}
