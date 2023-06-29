<?php

namespace CptnHook;

use CptnHook\FileSystem;
use Symfony\Component\Finder\Finder;

class DefaultFileSystem implements FileSystem
{
    public function filesInDirectory(string $dir): Finder
    {
        return Finder::create()
            ->in($dir)
            ->files()
            ->depth(0);
    }
}