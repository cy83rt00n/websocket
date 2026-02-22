<?php

use App\Core\Config\JsonConfig;
use App\Core\DI\DIContainer;
use App\Core\DI\DIContainerRegistrator;
use App\Core\DI\DIContainerAutowire;
use App\Core\DI\DIContainerResolver;
use App\Core\Interfaces\ConfigInterface;
use App\Sockets\WebSocket\Server;

require('vendor/autoload.php');

$registrator = new DIContainerRegistrator();
$resolver = new DIContainerResolver();
$container = new DIContainer($registrator, new DIContainerAutowire());
$registrator->singleton(ConfigInterface::class, $resolver->register(ConfigInterface::class, JsonConfig::class), ['file'=>__DIR__ . '/cfg/default.json']);
$server = new Server($container->get(ConfigInterface::class));
$server->start();