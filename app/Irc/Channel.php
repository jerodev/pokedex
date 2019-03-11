<?php

namespace App\Irc;

use App\Irc\Responders\Responder;
use ErrorException;
use Exception;
use Jerodev\PhpIrcClient\IrcChannel;

class Channel
{
    /** @var string */
    public $name;

    /** @var Responder[] */
    public $responders;

    /**
     *  Create a new channel instance.
     *
     *  @param string $name The name of the channel.
     *  @param Responder[] $responders An array of initial responders to use for this channel.
     */
    public function __construct(string $name, array $responders = [])
    {
        $this->name = $name;
        $this->responders = $responders;
    }

    /**
     *  Add a responder for this channel.
     *
     *  @param Responder $responder An object of the type Responder.
     */
    public function addResponder(Responder $responder): void
    {
        $this->responders[] = $responder;
    }

    /**
     *  Handle an incoming message to this channel.
     *
     *  @param string $from The nickname of the user who sent the message.
     *  @param string $message The message sent by this user.
     *
     *  @return null|string The message to send back to the channel, if any.
     */
    public function handlePrivmsg(string $from, IrcChannel $ircChannel, string $message): ?string
    {
        $response = null;
        foreach ($this->responders as $responder) {
            try {
                if ($response === null) {
                    $response = $responder->handlePrivmsg($from, $ircChannel, $message);
                } else {
                    $responder->handlePrivmsg($from, $ircChannel, $message, false);
                }
            } catch (Exception | ErrorException $e) {
                var_dump('ERROR: '.$e->getMessage());
            }
        }

        return $response;
    }
}
