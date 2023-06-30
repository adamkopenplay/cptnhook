<?php

namespace CptnHook\Tests\Unit\DTO;

use PHPUnit\Framework\TestCase;
use CptnHook\DTO\ResultList;
use Countable;
use ArrayAccess;
use DateTime;
use CptnHook\DTO\Result;
use InvalidArgumentException;
use IteratorAggregate;

class ResultListTest extends TestCase
{
    /**
     * The code treats this object as an array typically, so it must implement 
     * these.
     */
    public function test_it_implements_required_interfaces()
    {
        $resultList = ResultList::fromArray([]);

        $this->assertInstanceOf(IteratorAggregate::class, $resultList);
        $this->assertInstanceOf(Countable::class, $resultList);
        $this->assertInstanceOf(ArrayAccess::class, $resultList);
    }

    public function test_it_can_be_used_like_an_array()
    {
        $list = [
            $this->createMock(Result::class),
            $this->createMock(Result::class),
            $this->createMock(Result::class),
            $this->createMock(Result::class),
        ];

        $list = ResultList::fromArray($list);

        foreach ($list as $index => $result) {
            $this->assertSame($list[$index], $result);
        }

        $this->assertEquals(4, count($list));
        $list[] = $this->createMock(Result::class);
        $this->assertEquals(5, count($list));
    }

    /**
     * @dataProvider onlyAllowResultElementsInConstructData
     */
    public function test_it_only_allows_result_elements_in_construct(Callable $argBuilder, string $expectMessage)
    {
        $this->expectExceptionMessage($expectMessage);
        ResultList::fromArray($argBuilder($this));
    }

    public static function onlyAllowResultElementsInConstructData(): array
    {
        $resultClass = Result::class;
        return [
            [fn(TestCase $test) => ["not a result"], "Object for key '0' is not a $resultClass, got string"],
            [fn(TestCase $test) => [new DateTime], "Object for key '0' is not a $resultClass, got DateTime"],
            [function(TestCase $test) {
                return [$test->createMock(Result::class), new DateTime];
            }, "Object for key '1' is not a $resultClass, got DateTime"],
        ];
    }

    /**
     * @dataProvider onlyAllowResultElementsToBePushedInData
     */
    public function test_it_only_allows_results_to_be_added(Callable $elementBuilder, bool $shouldFail)
    {
        if ($shouldFail) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage("All elements must be of type: " . Result::class);
        }

        $list = ResultList::fromArray([]);
        $list[] = $elementBuilder($this);

        if (! $shouldFail) {
            $this->assertCount(1, $list);
        }
    }

    public static function onlyAllowResultElementsToBePushedInData(): array
    {
        return [
            [fn(TestCase $test) => "not a result", true],
            [fn(TestCase $test) => new DateTime, true],
            [function(TestCase $test) {
                return $test->createMock(Result::class);
            }, false],
        ];
    }
}