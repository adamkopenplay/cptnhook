<?php

use CptnHook\Runnable;
use Symfony\Component\Console\Output\OutputInterface;

return new class implements Runnable {
    public function run(OutputInterface $output) {
        $output->writeln("Running test hook: test-1/2023_06_30_141900_test.php");
    }
};
