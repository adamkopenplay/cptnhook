<?php

namespace CptnHook\Traits;

use Traversable;
use ArrayIterator;

trait ActsLikeArray {
    protected function __construct(
        protected array $items
    ) {}

    abstract protected static function getElementClass(): string;

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $elementClass = self::getElementClass();
        if (! $value instanceof $elementClass) {
            throw new \InvalidArgumentException("All elements must be of type: " . $elementClass);
        }

        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetExists(mixed $offset): bool {
        return isset($this->items[$offset]);
    }

    public function offsetUnset(mixed $offset): void {
        unset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): mixed {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    public function count(): int
    {
        return count($this->items);
    }

    protected static function guardArrayElementsAreAllCorrectType(array $items): void
    {
        $elementClass = self::getElementClass();

        foreach ($items as $k => $item) {
            if (! $item instanceof $elementClass) {
                $argType = gettype($item);
                if ($argType == 'object') {
                    $argType = get_class($item);
                }

                throw new \RuntimeException("Object for key '$k' is not a $elementClass, got $argType");
            }
        }
    }

    public static function fromArray(array $items): self
    {
        self::guardArrayElementsAreAllCorrectType($items);

        return new static($items);
    }

    public function getIterator(): Traversable {
        return new ArrayIterator($this->items);
    }
}