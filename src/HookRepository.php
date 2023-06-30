<?php

namespace CptnHook;

use CptnHook\Config;
use Symfony\Component\Finder\Finder;
use CptnHook\DTO\ResultList;
use Symfony\Component\Finder\SplFileInfo;
use CptnHook\Model\Hook;
use CptnHook\Model\HookList;

interface HookRepository
{
    public function allInGroup(string $group): HookList;
    public function save(string $group, string $name);
}