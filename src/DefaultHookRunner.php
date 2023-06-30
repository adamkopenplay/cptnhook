<?php

namespace CptnHook;

use CptnHook\Config;
use Symfony\Component\Finder\Finder;
use CptnHook\DTO\ResultList;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;
use CptnHook\DTO\Result;
use CptnHook\DTO\ResultStatus;
use Symfony\Component\Console\Output\OutputInterface;
use CptnHook\HookRunner;
use CptnHook\HookBuilder;

class DefaultHookRunner implements HookRunner
{
    public function __construct(
        protected Config $cfg,
        protected HookBuilder $builder,
        protected HookRepository $repo,
        protected FileSystem $fs,
    ) {}
    
    public function run(OutputInterface $output, string $group): ResultList
    {
        $results = ResultList::fromArray([]);

        $files = $this->fs->filesInDirectory($this->cfg->getPathForHooks($group))
            ->name('*.php')
            ->sortByName(true);

        $hooksFromDb = $this->repo->allInGroup($group);

        foreach ($files as $file) {
            if ($hooksFromDb->contains($group, $file->getBasename())) {
                continue;
            }

            $results[] = $this->runHook($output, $group, $file);
        }

        return $results;
    }

    protected function runHook(OutputInterface $output, string $group, SplFileInfo $file): Result
    {
        try {
            $fileName = $file->getBasename();
            $hook = $this->builder->fromFile($file);
            $output->writeln("Executing hook: $fileName");
            $start = microtime(true);
            $hook->run($output);
            $durationMs = (int) (microtime(true)-$start) * 1000;
            $this->repo->save($group, $fileName);
            $output->writeln("Hook finished: $fileName [$durationMs ms]");
        } catch (Throwable $e) {
            $output->writeln("Hook failed: $fileName [" . $e->getMessage() . "]");
            return Result::new(ResultStatus::FAILURE, $group, $fileName, null, $e);
        }

        return Result::new(ResultStatus::SUCCESS, $group, $fileName, $durationMs);
    }
}