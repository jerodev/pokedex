<?php

namespace App\Irc\Responders;

use App\Repositories\MessageRepository;

/**
 *  This just stores all messages to the database
 */
class Logger extends Responder
{
    public function handlePrivmsg(string $from, string $to, string $message, bool $respond = true): ?string
    {
        MessageRepository::logMessage($to, $from, $message);
        
        return null;
    }
}