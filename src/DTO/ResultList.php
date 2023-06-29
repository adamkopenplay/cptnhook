<?php

namespace CptnHook\DTO;

use Iterator;
use Countable;
use ArrayAccess;
use CptnHook\DTO\Result;
use CptnHook\Traits\ActsLikeArray;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

class ResultList implements IteratorAggregate, Countable, ArrayAccess
{
    use ActsLikeArray;

    protected static function getElementClass(): string
    {
        return Result::class;
    }
}