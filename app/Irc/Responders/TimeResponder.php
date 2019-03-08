<?php

namespace App\Irc\Responders;

use DateTime;
use DateTimeZone;
use Exception;
use Jerodev\PhpIrcClient\IrcChannel;

/**
 *  A responder that returns the time when a user uses the command `!time {timezone?}`.
 */
class TimeResponder extends Responder
{
    public function handlePrivmsg(string $from, IrcChannel $to, string $message, bool $respond = true): ?string
    {
        if ($respond === false || ($message !== '!time' && strstr($message, ' ', true) !== '!time')) {
            return null;
        }

        $date = new DateTime('now');

        $zone = trim(strstr($message, ' '));
        if (!empty($zone)) {
            try {
                $date = new DateTime('now', new DateTimeZone($zone));
            }
            catch (Exception $e) {
                return "Timezone `$zone` does not exist! Choose a timezone from this list: http://php.net/manual/en/timezones.php.";
            }
        }

        return $date->format('Y-m-d H:i:s');
    }
}