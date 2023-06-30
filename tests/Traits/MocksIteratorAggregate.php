<?php

namespace CptnHook\Tests\Traits;

use ArrayIterator;
use PHPUnit\Framework\MockObject\MockObject;

trait MocksIteratorAggregate
{
    protected function mockIteratorAggregateMethods(MockObject $mock, ArrayIterator $iterator)
    {
        $mock->expects($this->any())
            ->method('getIterator')
            ->willReturn($iterator);

        // $iteratorMock->expects($this->any())
        //     ->method('rewind')
        //     ->will(
        //         $this->returnCallback(
        //             function() use ($iterator) {
        //                 $iterator->rewind();
        //             }
        //         )
        //     );

        // $iteratorMock->expects($this->any())
        //     ->method('current')
        //     ->will(
        //         $this->returnCallback(
        //             function() use ($iterator) {
        //                 return $iterator->current();
        //             }
        //         )
        //     );

        // $iteratorMock->expects($this->any())
        //     ->method('key')
        //     ->will(
        //         $this->returnCallback(
        //             function() use ($iterator) {
        //                 return $iterator->key();
        //             }
        //         )
        //     );

        // $iteratorMock->expects($this->any())
        //     ->method('next')
        //     ->will(
        //         $this->returnCallback(
        //             function() use ($iterator) {
        //                 $iterator->next();
        //             }
        //         )
        //     );

        // $iteratorMock->expects($this->any())
        //     ->method('valid')
        //     ->will(
        //         $this->returnCallback(
        //             function() use ($iterator) {
        //                 return $iterator->valid();
        //             }
        //         )
        //     );

        // $iteratorMock->expects($this->any())
        //     ->method('count')
        //     ->will(
        //         $this->returnCallback(
        //             function() use ($iterator) {
        //                 return $iterator->count();
        //             }
        //         )
        //     );
    }
}