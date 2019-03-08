<?php

namespace App\Irc\Responders;

abstract class Responder
{
    /** @var array */
    private $cache;

    /**
     *  Handle a user message, either pm or in a channel.
     *  Only the response of first responder that returns a string will be sent back to the server.
     *
     *  @param string $from The nickname of the user who sent the message.
     *  @param string $to The destination of the message. If this is a channel, the string will start with `#`.
     *  @param string $message The message itself.
     *  @param bool $respond Will the response string be sent back to the irc server?
     *
     *  @return null|string The string that should be responded or `null` on no response.
     */
    public function handlePrivmsg(string $from, string $to, string $message, bool $respond = true): ?string
    {
        return null;
    }
}