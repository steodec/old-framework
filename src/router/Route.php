<?php

namespace Humbrain\Framework\router;

/**
 * Class Route
 * @package Humbrain\Framework\router
 * Objet to define a route
 */
class Route
{
    private string $name;
    /** @var callable|string */
    private mixed $callback;
    private array $parameters;

    public function __construct(string $name, string|callable $callback, array $parameters)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
    }

    /**
     * Get the route name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the callback
     * @return callable|string
     */
    public function getCallback(): callable|string
    {
        return $this->callback;
    }

    /**
     * Get the URL parameters
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
