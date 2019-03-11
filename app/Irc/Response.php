<?php

namespace App\Irc;

class Response
{
    /** @var string */
    private $message;

    /** @var null|string */
    private $target;

    public function __construct(string $message, ?string $target = null)
    {
        $this->message = $message;
        $this->target = $target;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTarget()
    {
        return $this->target;
    }
}
