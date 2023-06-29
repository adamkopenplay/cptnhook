<?php

namespace CptnHook\Integration\Laravel;

use CptnHook\HookBuilder as HookBuilderInterface;
use CptnHook\Runnable;
use Symfony\Component\Finder\SplFileInfo;
use ReflectionClass;
use RuntimeException;

class HookBuilder implements HookBuilderInterface
{
    public function fromFile(SplFileInfo $file): Runnable
    {
        $class = require_once $file;

        return $class;
    }
}