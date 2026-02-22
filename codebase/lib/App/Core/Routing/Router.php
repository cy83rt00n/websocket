<?php

namespace App\Core\Routing;

use App\Core\Interfaces\DIContainerInterface;
use ReflectionClass;
use Exception;

class Router
{
    private static array $request;
    private Schema $schema;
    private DIContainerInterface $container;

    public function __construct(Schema $schema, DIContainerInterface $container)
    {
        self::$request['uri'] = $_SERVER['REQUEST_URI'];
        self::$request['path'] = explode('?', self::$request['uri'])[0];
        self::$request['params'] = explode('?', self::$request['uri'])[1];
        $this->schema = $schema->parse($this);
        $this->container = $container;
    }

    public function route()
    {
        $reflection = new ReflectionClass($this->schema->controller_fqcn);
        // $doc_comment = $reflection
        //     ->getMethod($this->schema->controller_action)
        //     ->getDocComment()
        // ;
        // preg_match_all('~@route\s(.*)$~m',$doc_comment,$route);
        // echo "<pre>" . var_export($route, true) . "</pre>";
        // die();
        // $controller = new $this->schema->controller_fqcn();
        // $action = $this->schema->controller_action;
        if ($reflection->getMethod($this->schema->controller_action)->getNumberOfParameters() > 0) {
            return call_user_func_array(
                [
                    new $this->schema->controller_fqcn($this->container),
                    $this->schema->controller_action,
                ],
                $this->schema->arguments
            );
        }
        return call_user_func([
            new $this->schema->controller_fqcn($this->container),
            $this->schema->controller_action,
        ]);
    }

    /**
     * @return ?string
     */
    public function __get($name): mixed
    {
        return self::$request[$name] ?? null;
    }
}
