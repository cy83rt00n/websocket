<?php

namespace App\Core\Routing\Schemas;

use App\Core\Routing\Schema;
use App\Core\Routing\Router;

class ControllerActionParams extends Schema
{
    private string $controllers_namespace = '\App\Controllers\\';
    private string $controller_action_suffix = 'Action';
    private string $fallsback_controller_fqcn = 'App\Controllers\Fallback';
    private string $fallback_controller_action = 'indexAction';

    public function parse(Router $router): ControllerActionParams
    {
        $parts = explode(self::$separator, ltrim($router->path, '/'));
        $controller_fqcn = $this->controllers_namespace . ucfirst(array_shift($parts));
        $controller_action = array_shift($parts) . $this->controller_action_suffix;
        $isClass = class_exists($controller_fqcn);
        $isAction = $isClass && method_exists($controller_fqcn, $controller_action);
        $this->parts->controller_fqcn = $isClass
            ? $controller_fqcn
            : $this->fallsback_controller_fqcn;
        $this->parts->controller_action = $isAction
            ? $controller_action
            : $this->fallback_controller_action;
        $this->parts->arguments = $parts;
        return $this;
    }
}
