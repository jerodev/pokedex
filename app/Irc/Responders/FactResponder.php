<?php

namespace App\Irc\Responders;

use DateTime;
use DateTimeZone;
use Exception;

/**
 *  A responder that can learn and return facts
 */
class FactResponder extends Responder
{
    public function handlePrivmsg(string $from, string $to, string $message, bool $respond = true): ?string
    {
        if ($respond === false || $message[0] !== '!') {
            return null;
        }

        // Database stuff
    }
}