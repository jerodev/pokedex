<?php

namespace App\Irc\Responders;

use Jerodev\PhpIrcClient\IrcChannel;

abstract class Responder
{
    /** @var array */
    private $cache;

    /**
     *  Handle a user message, either pm or in a channel.
     *  Only the response of first responder that returns a string will be sent back to the server.
     *
     *  @param string $from The nickname of the user who sent the message.
     *  @param IrcChannel $channel The internal IrcChannel of the PhpIrcClient.
     *  @param string $message The message itself.
     *  @param bool $respond Will the response string be sent back to the irc server?
     *
     *  @return null|string The string that should be responded or `null` on no response.
     */
    public function handlePrivmsg(string $from, IrcChannel $channel, string $message, bool $respond = true): ?string
    {
        return null;
    }
}