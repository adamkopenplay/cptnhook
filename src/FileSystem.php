<?php

namespace CptnHook;

use Symfony\Component\Finder\Finder;

interface FileSystem
{
    public function filesInDirectory(string $dir): Finder;
}