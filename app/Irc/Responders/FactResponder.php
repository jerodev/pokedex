<?php

namespace App\Irc\Responders;

use App\Irc\Response;
use App\Repositories\FactRepository;
use App\Repositories\MessageRepository;
use Jerodev\PhpIrcClient\IrcChannel;

/**
 *  A responder that can learn and tell facts.
 */
class FactResponder extends Responder
{
    /** @var FactRepository */
    private $factRepository;

    /** @var MessageRepository */
    private $messageRepository;

    public function __construct(?FactRepository $factRepository = null, ?MessageRepository $messageRepository = null)
    {
        $this->factRepository = $factRepository ?? app(FactRepository::class);
        $this->messageRepository = $messageRepository ?? app(MessageRepository::class);
    }

    public function handlePrivmsg(string $from, IrcChannel $to, string $message, bool $respond = true): ?Response
    {
        if ($respond === false) {
            return null;
        }

        if ($message[0] === '!') {

            // Fact stats
            if ($message === '!facts') {
                return $this->factStats($to, $message);
            }

            // Undo last fact
            if ($message === '!undo') {
                return $this->undoFact($to, $from);
            }

            // Fact stats
            if (strstr($message, ' ', true) === '!fact') {
                return $this->singleFactStats($to, $message);
            }

            // Quote a user
            if (strstr($message, ' ', true) === '!quote') {
                return $this->quoteUser($from, $to, trim(strstr($message, ' ')));
            }

            // Find a fact
            return $this->respondToFact($from, $to, $message);
        }

        // Learn a fact
        if (substr($message, 0, strlen('Pokedex: !')) === 'Pokedex: !') {
            return $this->learnFact($from, $to, $message);
        }

        return null;
    }

    private function factStats(IrcChannel $channel): ?Response
    {
        $stats = $this->factRepository->getStats($channel->getName());
        if (!$stats) {
            return null;
        }

        return new Response("I know $stats->fact_count facts. The last fact was `!$stats->command` created by $stats->nickname on $stats->created_at.");
    }

    private function learnFact(string $from, IrcChannel $to, string $message): ?Response
    {
        $message = substr($message, strlen('Pokedex: !'));
        $command = strstr($message, ' ', true);
        $response = trim(substr($message, strlen($command)));

        $this->factRepository->learnFact($from, $to->getName(), $command, $response);

        return null;
    }

    private function quoteUser(string $from, IrcChannel $to, string $userToQuote): ?Response
    {
        $message = $this->messageRepository->getLastUserMessage($to->getName(), $userToQuote);
        if ($message === null) {
            return new Response("No quotable message found for $userToQuote in the last 5 minutes.");
        }

        $this->learnFact($from, $to, "Pokedex: !$userToQuote <$userToQuote> $message->message");

        return new Response("Saved quote \"$message->message\" to `!$userToQuote`.");
    }

    private function respondToFact(string $from, IrcChannel $to, string $message): ?Response
    {
        $command = substr((strpos($message, ' ') !== false ? strstr($message, ' ', true) : $message), 1);
        if (!empty($command) && $response = $this->factRepository->getResponseString($command, $to->getName(), true)) {
            return new Response($this->parseResponse($response, $from, $to, $message));
        }

        return null;
    }

    private function singleFactStats(IrcChannel $to, string $message): ?Response
    {
        $command = trim(strstr($message, ' '));
        if (strpos($command, ' ') === false && ($stats = $this->factRepository->getSingleStats($command, $to->getName()))) {
            return new Response(
                "`!$command` was created on $stats->created_at, has $stats->response_count response".($stats->response_count > 1 ? 's' : '')." and has been used $stats->uses times."
            );
        }

        return null;
    }

    private function parseResponse(string $response, string $user, IrcChannel $channel, string $message): string
    {
        // Replace %randomuser% with a random user in the channel.
        if (strpos($response, '%randomuser%') !== false) {
            $response = str_replace('%randomuser%', $channel->getUsers()[array_rand($channel->getUsers())], $response);
        }

        // Replace %user% with the current users nickname.
        $response = str_replace('%user%', $user, $response);

        // Replace %param% with the payload after the command
        $param = trim(strstr($message, ' '));
        $response = str_replace('%param%', $param, $response);

        // Replace %param:fallback% with the payload or fallback if there is no payload
        if (strpos($response, '%param:') !== false) {
            $response = preg_replace_callback('/%param:([^%]+)%/', function ($matches) use ($param) {
                if (!empty($param)) {
                    return $param;
                } else {
                    return $matches[1];
                }
            }, $response);
        }

        // Replace %dice:x:y% with a random number between x and y.
        if (strpos($response, '%dice:') !== false) {
            $response = preg_replace_callback('/%dice:(\d+):(\d+)%/', function ($matches) {
                return rand($matches[1], $matches[2]);
            }, $response);
        }

        return $response;
    }

    private function undoFact(IrcChannel $channel, string $user): ?Response
    {
        $fact = $this->factRepository->getLastUserFact($user, $channel->getName(), 30);
        if (!$fact) {
            return new Response('You did not create a fact in the last 30 minutes on this channel!', $user);
        }

        $this->factRepository->removeFact($fact->id);

        return new Response("The fact `!$fact->command` with response \"$fact->response\" has been removed.", $user);
    }
}
