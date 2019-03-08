<?php

namespace App\Irc\Responders;

use App\Repositories\MessageRepository;
use Jerodev\PhpIrcClient\IrcChannel;

/**
 *  This just stores all messages to the database
 */
class Logger extends Responder
{
    public function handlePrivmsg(string $from, IrcChannel $to, string $message, bool $respond = true): ?string
    {
        MessageRepository::logMessage($to->getName(), $from, $message);

        return null;
    }
}