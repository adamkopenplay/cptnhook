<?php

namespace CptnHook\Tests\Unit;

use PHPUnit\Framework\TestCase;
use CptnHook\DefaultHookRunner;
use CptnHook\FileSystem;
use CptnHook\HookRepository;
use CptnHook\HookBuilder;
use CptnHook\Config;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use CptnHook\Tests\Traits\MocksIteratorAggregate;
use ArrayIterator;
use Symfony\Component\Finder\SplFileInfo;
use CptnHook\Model\HookList;
use CptnHook\Runnable;
use CptnHook\Tests\Stubs\HookClass;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\Comparator\ComparisonFailure;
use RuntimeException;

class DefaultHookRunnerTest extends TestCase
{
    use MocksIteratorAggregate;

    public function test_when_there_are_no_files()
    {
        $cfg = $this->createMock(Config::class);
        $builder = $this->createMock(HookBuilder::class);
        $repo = $this->buildRepo([]);
        $fs = $this->buildFs([]);

        $runner = new DefaultHookRunner(
            $cfg,
            $builder,
            $repo,
            $fs,
        );

        $output = $this->createMock(OutputInterface::class);
        $results = $runner->run($output, "group");

        $this->assertCount(0, $results);
    }

    public function test_when_all_files_are_run()
    {
        $cfg = $this->createMock(Config::class);
        $builder = $this->createMock(HookBuilder::class);
        $repo = $this->buildRepo([
            "some_hook.php"
        ]);
        $fs = $this->buildFs([
            "some_hook.php"
        ]);

        $runner = new DefaultHookRunner(
            $cfg,
            $builder,
            $repo,
            $fs,
        );

        $output = $this->createMock(OutputInterface::class);
        $results = $runner->run($output, "group");

        $this->assertCount(0, $results);
    }

    public function test_when_a_single_hook_runs()
    {
        $cfg = $this->createMock(Config::class);
        $builder = $this->buildHookBuilder([]);
        $repo = $this->buildRepo([]);
        $fs = $this->buildFs([
            "some_hook.php"
        ]);

        $runner = new DefaultHookRunner(
            $cfg,
            $builder,
            $repo,
            $fs,
        );

        $output =  $this->buildOutput([
            "Executing hook: some_hook.php",
            "running: some_hook.php",
            function($msg) {
                return str_starts_with($msg, "Hook finished: some_hook.php");
            },
        ]);
        $results = $runner->run($output, "group");

        $this->assertCount(1, $results);
        foreach ($results as $result) {
            $this->assertFalse($result->failed());
        }
    }

    public function test_when_multiple_hooks_run()
    {
        $cfg = $this->createMock(Config::class);
        $builder = $this->buildHookBuilder([]);
        $repo = $this->buildRepo([]);
        $fs = $this->buildFs([
            "hook_1.php",
            "hook_2.php",
            "hook_3.php",
        ]);

        $runner = new DefaultHookRunner(
            $cfg,
            $builder,
            $repo,
            $fs,
        );

        $output =  $this->buildOutput([
            "Executing hook: hook_1.php",
            "running: hook_1.php",
            function($msg) {
                return str_starts_with($msg, "Hook finished: hook_1.php");
            },
            "Executing hook: hook_2.php",
            "running: hook_2.php",
            function($msg) {
                return str_starts_with($msg, "Hook finished: hook_2.php");
            },
            "Executing hook: hook_3.php",
            "running: hook_3.php",
            function($msg) {
                return str_starts_with($msg, "Hook finished: hook_3.php");
            },
        ]);
        $results = $runner->run($output, "group");

        $this->assertCount(3, $results);
        foreach ($results as $result) {
            $this->assertFalse($result->failed());
        }
    }

    public function test_when_multiple_hooks_run_but_some_are_in_db()
    {
        $cfg = $this->createMock(Config::class);
        $builder = $this->buildHookBuilder([]);
        $repo = $this->buildRepo([
            "hook_1.php",
        ]);
        $fs = $this->buildFs([
            "hook_1.php",
            "hook_2.php",
            "hook_3.php",
        ]);

        $runner = new DefaultHookRunner(
            $cfg,
            $builder,
            $repo,
            $fs,
        );

        $output =  $this->buildOutput([
            "Executing hook: hook_2.php",
            "running: hook_2.php",
            function($msg) {
                return str_starts_with($msg, "Hook finished: hook_2.php");
            },
            "Executing hook: hook_3.php",
            "running: hook_3.php",
            function($msg) {
                return str_starts_with($msg, "Hook finished: hook_3.php");
            },
        ]);
        $results = $runner->run($output, "group");

        $this->assertCount(2, $results);
        foreach ($results as $result) {
            $this->assertFalse($result->failed());
        }
    }

    public function test_when_hook_errors_others_carry_on()
    {
        $cfg = $this->createMock(Config::class);
        $builder = $this->buildHookBuilder([
            "hook_2.php" => new RuntimeException("hook_2.php failed"),
        ]);
        $repo = $this->buildRepo([
            "hook_1.php",
        ]);
        $fs = $this->buildFs([
            "hook_1.php",
            "hook_2.php",
            "hook_3.php",
        ]);

        $runner = new DefaultHookRunner(
            $cfg,
            $builder,
            $repo,
            $fs,
        );

        $output =  $this->buildOutput([
            "Executing hook: hook_2.php",
            "running: hook_2.php",
            "Hook failed: hook_2.php [hook_2.php failed]",
            "Executing hook: hook_3.php",
            "running: hook_3.php",
            function($msg) {
                return str_starts_with($msg, "Hook finished: hook_3.php");
            },
        ]);
        $results = $runner->run($output, "group");

        $this->assertCount(2, $results);

        $this->assertTrue($results[0]->failed());
        $this->assertFalse($results[1]->failed());
        
    }

    protected function buildHookBuilder(array $errors): HookBuilder
    {
        $builder = $this->getMockBuilder(HookBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->any())
            ->method('fromFile')
            ->willReturnCallback(function(SplFileInfo $file) use ($errors) {
                $name = $file->getBasename();
                return new HookClass($file, $errors[$name] ?? null);
            });

        return $builder;
    }

    protected function buildOutput(array $outputExpectations): OutputInterface
    {
        $outputCalls = 0;

        $outputCallback = $this->callback(function(string $msg) use($outputExpectations, &$outputCalls) {
            if (is_callable($outputExpectations[$outputCalls])) {
                $retVal = $outputExpectations[$outputCalls]($msg);
            } else {
                $retVal = $outputExpectations[$outputCalls] == $msg;
            } 
            
            if ($retVal !== true) {
                $expected = $outputExpectations[$outputCalls];
                if (is_callable($outputExpectations[$outputCalls])) {
                    $expected = "<callback comparator>";
                }
                
                $failure = new ComparisonFailure($expected, $msg, $expected, $msg);
                throw new ExpectationFailedException("output->writeln failed at call $outputCalls", $failure);
            }
            
            $outputCalls++;
            return $retVal;
        });

        $output =  $this->getMockBuilder(OutputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $output->expects($this->any())
            ->method('writeln')
            ->with($outputCallback);

        return $output;
    }
    protected function buildRepo(array $hooks): HookRepository
    {
        $repo = $this->getMockBuilder(HookRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $hookList = $this->getMockBuilder(HookList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $hookList->expects($this->any())
            ->method('contains')
            ->willReturnCallback(function($group, $name) use($hooks) {
                return in_array($name, $hooks);
            });

        $repo->expects($this->once())
            ->method('allInGroup')
            ->willReturn($hookList);

        return $repo;
    }

    protected function buildFs(array $files): FileSystem
    {
        $fs = $this->getMockBuilder(FileSystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $finder = $this->getMockBuilder(Finder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $finder->expects($this->exactly(1))
            ->method('name')
            ->with('*.php')
            ->willReturn($finder);

        $finder->expects($this->exactly(1))
            ->method('sortByName')
            ->with(true)
            ->willReturn($finder);

        $fileMocks = [];
        foreach ($files as $file) {
            $fileMock = $this->getMockBuilder(SplFileInfo::class)
                ->disableOriginalConstructor()
                ->getMock();
            $fileMock->expects($this->any())
                ->method('getBasename')
                ->willReturn($file);

            $fileMocks[] = $fileMock;
        }
        $this->mockIteratorAggregateMethods($finder, new ArrayIterator($fileMocks));

        $fs->expects($this->exactly(1))
            ->method('filesInDirectory')
            ->willReturn($finder);

        return $fs;
    }
}