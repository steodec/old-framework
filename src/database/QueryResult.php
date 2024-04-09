<?php

namespace Humbrain\Framework\database;

use ArrayAccess;
use Iterator;

class QueryResult implements ArrayAccess, Iterator
{
    private array $records;
    private array $hydratedRecords = [];
    private ?string $entity;
    private int $position = 0;

    public function __construct(array $data, ?string $entity = null)
    {
        $this->records = $data;
        $this->entity = $entity;
    }

    /**
     * Return the results as an array of objects
     * @return array
     */
    public function get(int $index): mixed
    {
        if ($this->entity) :
            if (!isset($this->hydratedRecords[$index])) :
                $this->hydratedRecords[$index] = Hydrator::hydrate($this->entity, $this->records[$index]);
            endif;
            return $this->hydratedRecords[$index];
        endif;
        return $this->all()[$index];
    }

    /**
     * @inheritDoc
     */
    public function current(): mixed
    {
        return $this->get($this->position);
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        $this->position++;
    }

    /**
     * @inheritDoc
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return isset($this->records[$this->position]);
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->records[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new Exception("Can't alter records");
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new Exception("Can't alter records");
    }
}