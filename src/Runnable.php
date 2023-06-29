<?php

namespace CptnHook;

use Symfony\Component\Console\Output\OutputInterface;

interface Runnable
{
    public function run(OutputInterface $output);
}