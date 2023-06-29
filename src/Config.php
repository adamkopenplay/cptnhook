<?php

namespace CptnHook;

interface Config
{
    public function getPathForHooks(string $group): string;
}