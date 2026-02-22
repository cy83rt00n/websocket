<?php

namespace App\Core\Config;

use App\Core\Interfaces\ConfigInterface;
use App\Core\Interfaces\DumpableInterface;
use Exception;

class JsonConfig implements ConfigInterface, DumpableInterface
{
    private array $config = [];

    public function __construct(string $file)
    {
        $this->load($file);
    }

    public function load(string $file): bool
    {
        if ($config = file_get_contents($file)) {
            $this->config = json_decode($config, true);
            return true;
        } else {
            throw new Exception("Cannot load config file " . $file);
        }
    }

    public function has(string $key): bool
    {
        $parts = explode('.', $key);
        $current = $this->config;
        for ($i = 0;$i < sizeof($parts);$i++) {
            if(!isset($current[$parts[$i]])) return false;
            $current = $current[$parts[$i]];
        }
        return true;
    }

    public function get(string $key): mixed
    {
        $parts = explode('.', $key);
        $current = $this->config;
        for ($i = 0;$i < sizeof($parts);$i++) {
            if(!isset($current[$parts[$i]])) return false;
            $current = $current[$parts[$i]];
        }
        return $current;
    }

    public function dump(): void {
        var_dump($this->config);
    }
}
