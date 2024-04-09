<?php

namespace Humbrain\Framework\sessions;

use ArrayAccess;

class PhpSession implements SessionInterface, ArrayAccess
{

    /**
     * Check if the session is started
     * @return void
     */
    private function ensureStarted(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }
        session_start();
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->ensureStarted();
        if (!array_key_exists($key, $_SESSION)) {
            return $default;
        }
        return $_SESSION[$key];
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }

    public function offsetExists(mixed $offset): bool
    {
        $this->ensureStarted();
        return array_key_exists($offset, $_SESSION);
    }

    public function offsetGet(mixed $offset): mixed
    {
        $this->ensureStarted();
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->ensureStarted();
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->ensureStarted();
        $this->delete($offset);
    }
}
