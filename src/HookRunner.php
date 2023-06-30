<?php

namespace CptnHook;

use CptnHook\DTO\ResultList;
use Symfony\Component\Console\Output\OutputInterface;

interface HookRunner
{
    public function run(OutputInterface $output, string $group): ResultList;
}