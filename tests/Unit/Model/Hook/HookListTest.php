<?php

namespace CptnHook\Tests\Unit\Model\Hook;

use PHPUnit\Framework\TestCase;
use CptnHook\Model\HookList;
use Countable;
use ArrayAccess;
use DateTime;
use CptnHook\Model\Hook;
use InvalidArgumentException;
use IteratorAggregate;

class HookListTest extends TestCase
{
    /**
     * The code treats this object as an array typically, so it must implement 
     * these.
     */
    public function test_it_implements_required_interfaces()
    {
        $hookList = HookList::fromArray([]);

        $this->assertInstanceOf(IteratorAggregate::class, $hookList);
        $this->assertInstanceOf(Countable::class, $hookList);
        $this->assertInstanceOf(ArrayAccess::class, $hookList);
    }

    public function test_it_can_be_used_like_an_array()
    {
        $list = [
            $this->createMock(Hook::class),
            $this->createMock(Hook::class),
            $this->createMock(Hook::class),
            $this->createMock(Hook::class),
        ];

        $list = HookList::fromArray($list);

        foreach ($list as $index => $result) {
            $this->assertSame($list[$index], $result);
        }

        $this->assertEquals(4, count($list));
        $list[] = $this->createMock(Hook::class);
        $this->assertEquals(5, count($list));
    }

    /**
     * @dataProvider onlyAllowHookElementsInConstructData
     */
    public function test_it_only_allows_hook_elements_in_construct(Callable $argsBuilder, string $expectMessage)
    {
        $this->expectExceptionMessage($expectMessage);
        HookList::fromArray($argsBuilder($this));
    }

    public static function onlyAllowHookElementsInConstructData(): array
    {
        $hookClass = Hook::class;
        return [
            [fn(TestCase $test) => ["not a hook"], "Object for key '0' is not a $hookClass, got string"],
            [fn(TestCase $test) => [new DateTime], "Object for key '0' is not a $hookClass, got DateTime"],
            [function (TestCase $test) {
                return [$test->createMock(Hook::class), new DateTime];
             }, "Object for key '1' is not a $hookClass, got DateTime"],
        ];
    }

    /**
     * @dataProvider onlyAllowHookElementsToBePushedInData
     */
    public function test_it_only_allows_hooks_to_be_added(mixed $elementBuilder, bool $shouldFail)
    {
        if ($shouldFail) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage("All elements must be of type: " . Hook::class);
        }

        $list = HookList::fromArray([]);
        $list[] = $elementBuilder($this);

        if (! $shouldFail) {
            $this->assertCount(1, $list);
        }
    }

    public static function onlyAllowHookElementsToBePushedInData(): array
    {
        return [
            [fn(TestCase $test) => "not a hook", true],
            [fn(TestCase $test) => new DateTime, true],
            [fn(TestCase $test) => $test->createMock(Hook::class), false],
        ];
    }

    public function test_contains_finds_elements_correctly()
    {
        $hooks = [
            $this->mockHook("group1", "name1"),
            $this->mockHook("group1", "name2"),
            $this->mockHook("group2", "name1"),
            $this->mockHook("group2", "name2"),
        ];
        $list = HookList::fromArray($hooks);

        $this->assertTrue($list->contains("group1", "name1"));
        $this->assertTrue($list->contains("group1", "name2"));
        $this->assertTrue($list->contains("group2", "name1"));
        $this->assertTrue($list->contains("group2", "name2"));

        $this->assertFalse($list->contains("group1", "name3"));
        $this->assertFalse($list->contains("group3", "name1"));
        $this->assertFalse($list->contains("blah", "meh"));
    }

    protected function mockHook(string $group, string $name): Hook
    {
        $mock = $this->getMockBuilder(Hook::class)  
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        $mock->expects($this->any())
            ->method('getGroup')
            ->willReturn($group);

        return $mock;
    }
}