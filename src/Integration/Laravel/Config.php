<?php

namespace CptnHook\Integration\Laravel;

use CptnHook\Config as ConfigInterface;
use Exception;

class Config implements ConfigInterface
{
    public function __construct(
        protected array $config
    ) {}

    public function getPathForHooks(string $group): string
    {
        if (! array_key_exists($group, $this->config['groups'])) {
            throw new Exception("unknown group: $group");
        };

        return $this->config['groups'][$group];
    }
}