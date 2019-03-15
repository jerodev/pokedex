<?php

namespace App\Irc\Responders;

use App\Irc\Response;
use App\Repositories\MessageRepository;
use Jerodev\PhpIrcClient\IrcChannel;

/**
 *  This just stores all messages to the database.
 */
class Logger extends Responder
{
    /** @var MessageRepository */
    private $messageRepository;

    public function __construct(?MessageRepository $messageRepository = null)
    {
        $this->messageRepository = $messageRepository ?? app(MessageRepository::class);
    }

    public function handlePrivmsg(string $from, IrcChannel $to, string $message, bool $respond = true): ?Response
    {
        $this->messageRepository->logMessage($to->getName(), $from, $message);

        return null;
    }
}
