<?php

use App\Core\Config\JsonConfig;
use App\Core\DI\DIContainer;
use App\Core\DI\DIContainerRegistrator;
use App\Core\DI\DIContainerAutowire;
use App\Core\DI\DIContainerResolver;
use App\Core\Interfaces\ConfigInterface;
use App\Core\Templaters\LWTemplater\LWTemplater;

require('vendor/autoload.php');

$registrator = new DIContainerRegistrator();
$resolver = new DIContainerResolver();
$container = new DIContainer($registrator, new DIContainerAutowire());
$registrator->singleton(ConfigInterface::class, $resolver->register(ConfigInterface::class, JsonConfig::class), ['file'=>__DIR__ . '/cfg/default.json']);

$config  = $container->get(ConfigInterface::class);
$ws_host = $config->get('services.ws.host');
$ws_port = $config->get('services.ws.port');

LWTemplater::view('templates/index.html', [
    'ws_url' => 'ws://' . $ws_host . ':' . $ws_port
]);