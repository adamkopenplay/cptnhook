<?php

namespace CptnHook\Tests\Unit\DTO;

use PHPUnit\Framework\TestCase;
use CptnHook\DTO\ResultList;
use Countable;
use Iterator;
use ArrayAccess;
use DateTime;
use CptnHook\DTO\Result;
use InvalidArgumentException;
use CptnHook\DTO\ResultStatus;

class ResultTest extends TestCase
{
    public function test_getters()
    {
        $e = new \Exception("fake error");
        $result = Result::new(ResultStatus::FAILURE, "blah", "meh", 1200, $e);

        $this->assertEquals("blah/meh", $result->getName());
        $this->assertSame($e, $result->getError());
        $this->assertEquals(1200, $result->getDurationMs());
    }

    public function test_it_determines_failure()
    {
        $result = Result::new(ResultStatus::FAILURE, "blah", "meh", 0);
        $this->assertTrue($result->failed());

        $result = Result::new(ResultStatus::SUCCESS, "blah", "meh", 0);
        $this->assertFalse($result->failed());
    }
}