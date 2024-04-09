<?php

namespace Humbrain\Framework\sessions;

interface SessionInterface
{
    /**
     * Get a session value
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default): mixed;

    /**
     * Set a session value
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void;

    /**
     * Delete a session value
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;
}
