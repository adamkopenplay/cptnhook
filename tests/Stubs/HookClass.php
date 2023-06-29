<?php

namespace CptnHook\Tests\Stubs;

use Symfony\Component\Finder\SplFileInfo;
use CptnHook\Runnable;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/*
Actual hooks that are configured in the app shouldn't have a constructor. But for
the purposes of tests, we need a way to log information so that we know it's running.
*/
class HookClass implements Runnable {
    public function __construct(
        protected SplFileInfo $file,
        protected ?Throwable $error
    ) {}

    public function run(OutputInterface $output) {
        $name = $this->file->getBasename();
        $output->writeln("running: " . $name);

        if ($this->error !== null) {
            throw $this->error;
        }

        return;
    
    }
}