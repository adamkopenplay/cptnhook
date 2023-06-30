<?php

namespace CptnHook\Integration\Laravel\Command;

use Illuminate\Console\Command;
use CptnHook\HookRunner;

class RunHooksCommand extends Command
{
    protected $signature = 'cptnhook:run {group} {--debug}';

    protected $description = 'Run outstanding hooks in group.';

    public function handle(HookRunner $runner)
    {
        $results = $runner->run($this->output, $this->input->getArgument('group'));

        if (count($results) == 0) {
            $this->info("No hooks to run.");
            return true;
        }

        $rows = [];

        foreach ($results as $result) {
            $row = [
                'NAME' => $result->getName(),
                'STATUS' => $result->failed() ? "failed" : "succeeded",
                'DURATION (ms)' => $result->getDurationMs(),
                'ERROR' => $result->failed() && $result->getError() !== null ? $result->getError()->getMessage() : '',
            ];

            if ($this->option('debug') && $result->getError() !== null) {
                $row['STACK TRACE'] = $result->getError()->getTraceAsString();
            }

            $rows[] = $row;
        }

        $headers = ["NAME", "STATUS", "DURATION (ms)", "ERROR"];

        if ($this->option('debug')) {
            $headers[] = "STACK TRACE";
        }

        $this->table(
            ["NAME", "STATUS", "DURATION (ms)", "ERROR"],
            $rows,
        );
    }
}