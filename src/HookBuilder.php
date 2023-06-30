<?php

namespace CptnHook;

use Symfony\Component\Finder\SplFileInfo;

interface HookBuilder
{
    public function fromFile(SplFileInfo $filePath): Runnable;
}