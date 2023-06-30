<?php

namespace CptnHook\Model;

use Iterator;
use Countable;
use ArrayAccess;
use CptnHook\Model\Hook;
use CptnHook\Traits\ActsLikeArray;
use IteratorAggregate;

class HookList implements IteratorAggregate, Countable, ArrayAccess
{
    use ActsLikeArray;

    protected static function getElementClass(): string
    {
        return Hook::class;
    }

    public function contains(string $group, string $hookName)
    {
        $matching = array_filter($this->items, fn($hook) => $hook->getName() == $hookName && $hook->getGroup() == $group);

        return count($matching) > 0;
    }
}