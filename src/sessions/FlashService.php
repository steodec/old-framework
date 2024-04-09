<?php

namespace Humbrain\Framework\sessions;

class FlashService
{
    private const SESSION_KEY = 'flash';
    private SessionInterface $session;
    private mixed $message = null;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function success(string $message): void
    {
        $flash = $this->session->get(self::SESSION_KEY, []);
        $flash['success'] = $message;
        $this->session->set(self::SESSION_KEY, $flash);
    }

    public function get(string $type): ?string
    {
        if (is_null($this->message)) :
            $this->message = $this->session->get(self::SESSION_KEY, []);
            $this->session->delete(self::SESSION_KEY);
        endif;
        if (array_key_exists($type, $this->message)) :
            return $this->message[$type];
        endif;
        return null;
    }

    public function error(string $message): void
    {
        $flash = $this->session->get(self::SESSION_KEY, []);
        $flash['error'] = $message;
        $this->session->set(self::SESSION_KEY, $flash);
    }
}
