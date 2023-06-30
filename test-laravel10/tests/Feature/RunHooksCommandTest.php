<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use CptnHook\Integration\Laravel\Model\Hook;
use Illuminate\Support\Facades\Artisan;

class RunHooksCommandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_command_runs_successfully(): void
    {
        $statusCode = $this->withoutMockingConsoleOutput()->artisan('cptnhook:run test-1');
        $this->assertEquals(0, $statusCode);

        /*
        * This is likely to be flakey due to the duration, we may need to do 
        * something smarter.
        */
        $expectedOutput = "Executing hook: 2023_06_30_141900_test.php
Running test hook: test-1/2023_06_30_141900_test.php
Hook finished: 2023_06_30_141900_test.php [0 ms]
+-----------------------------------+-----------+---------------+-------+
| NAME                              | STATUS    | DURATION (ms) | ERROR |
+-----------------------------------+-----------+---------------+-------+
| test-1/2023_06_30_141900_test.php | succeeded | 0             |       |
+-----------------------------------+-----------+---------------+-------+
";

        $this->assertEquals($expectedOutput, Artisan::output());
        $this->assertDatabaseHas(config('cptnhook.tableName', Hook::DEFAULT_TABLE_NAME), [
            'name' => '2023_06_30_141900_test.php',
            'group' => 'test-1',
        ]);
    }
}
