<?php

namespace App\Irc\Responders;

use App\Irc\Response;
use DateTime;
use DateTimeZone;
use Exception;
use Jerodev\PhpIrcClient\IrcChannel;

/**
 *  `!time {timezone?}`
 *  A responder that returns the time when a user uses the command.
 */
class TimeResponder extends Responder
{
    public function handlePrivmsg(string $from, IrcChannel $to, string $message, bool $respond = true): ?Response
    {
        if ($respond === false || ($message !== '!time' && strstr($message, ' ', true) !== '!time')) {
            return null;
        }

        $date = new DateTime('now');

        $zone = trim(strstr($message, ' '));
        if (!empty($zone)) {
            try {
                $date = new DateTime('now', new DateTimeZone($zone));
            } catch (Exception $e) {
                return new Response("Timezone `$zone` does not exist! Choose a timezone from this list: http://php.net/manual/en/timezones.php.");
            }
        }

        return new Response($date->format('Y-m-d H:i:s'));
    }
}
