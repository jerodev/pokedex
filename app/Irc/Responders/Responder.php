<?php

namespace App\Irc\Responders;

use App\Irc\Response;
use Jerodev\PhpIrcClient\IrcChannel;

abstract class Responder
{
    /**
     *  Remembers when functions have been called to enable throttling.
     *
     *  @var array
     */
    private $throttleCache;

    /**
     *  Handle a user message, either pm or in a channel.
     *  Only the response of first responder that returns a string will be sent back to the server.
     *
     *  @param string $from The nickname of the user who sent the message.
     *  @param IrcChannel $channel The internal IrcChannel of the PhpIrcClient.
     *  @param string $message The message itself.
     *  @param bool $respond Will the response string be sent back to the irc server?
     *
     *  @return null|Response A response object to be send back to the irc server or `null` on no response.
     */
    abstract public function handlePrivmsg(string $from, IrcChannel $channel, string $message, bool $respond = true): ?Response;

    /**
     *  Throttle a certain function.
     *
     *  @param string $slug A slug to distinguish different throttles.
     *  @param int $time The time in seconds to count usage.
     *  @param int $limit The times a function can be called in the defined time.
     *  @param callable $function The function to call when it is not throttled.
     *
     *  @return null|Response The response to send back to the server.
     */
    protected function throttle(string $slug, int $time, int $limit, callable $function, ?string $nickname = null): ?Response
    {
        if ($this->throttleCache !== null && array_key_exists($slug, $this->throttleCache)) {
            $calls = array_filter($this->throttleCache[$slug], function ($call) use ($time) {
                return $call >= time() - $time;
            });

            if (count($calls) >= $limit) {
                $minutes = round($time / 60);

                if ($nickname !== null) {
                    return new Response("This command can only be executed $limit times every $minutes minutes.", $nickname);
                } else {
                    return null;
                }
            }

            $this->throttleCache[$slug] = $calls;
        }

        $this->throttleCache[$slug][] = time();

        return $function();
    }
}
