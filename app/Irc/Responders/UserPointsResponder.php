<?php

namespace App\Irc\Responders;

use App\Irc\Response;
use App\Repositories\UserPointsRepository;
use Jerodev\PhpIrcClient\IrcChannel;

/**
 *  Implements a karma system on an irc channel.
 */
class UserPointsResponder extends Responder
{
    /**
     *  The number of votes a user can cast each day.
     *
     *  @var int
     */
    private $dailyVotes = 3;

    /** @var UserPointsRepository */
    private $userPointsRepository;

    public function __construct(?UserPointsRepository $userPointsRepository = null)
    {
        $this->userPointsRepository = $userPointsRepository ?? app(UserPointsRepository::class);
    }

    public function handlePrivmsg(string $from, IrcChannel $to, string $message, bool $respond = true): ?Response
    {
        if ($respond === false || $message[0] !== '!') {
            return null;
        }

        $command = ltrim(strstr($message, ' ', true) ?: $message, '!');
        switch ($command) {
            case 'downvote':
            case 'upvote':
                return $this->recordVote($from, $message, $to->getName(), $command === 'upvote');
                break;

            case 'points':
                return $this->returnUserPoints($message, $to->getName(), $from);
                break;

            case 'topusers':
                break;

            case 'badusers':
                break;
        }

        return null;
    }

    private function returnUserPoints(string $message, string $channel, string $from): ?Response
    {
        $user = trim(strstr($message, ' '));
        if (strpos($user, ' ') !== false) {
            return null;
        }
        if (empty($user)) {
            $user = $from;
        }

        $score = $this->userPointsRepository->getUserScore($user, $channel);

        return new Response("$user has a karma score of $score.");
    }

    private function recordVote(string $from, string $message, string $channel, bool $is_upvote)
    {
        // Validate the user that is voted on
        $to = trim(strstr($message, ' '));
        if (strpos($to, ' ') !== false || empty($to)) {
            return;
        } elseif ($from === $to) {
            return new Response('Self voting is not allowed!', $from);
        }

        $voteCount = $this->userPointsRepository->countUserVotes($from, true);
        if ($voteCount >= $this->dailyVotes) {
            return new Response("You can only cast $this->dailyVotes votes each day.", $from);
        }

        $this->userPointsRepository->castVote($from, $to, $channel, $is_upvote);
        
        return $this->returnUserPoints($message, $channel, $from);
    }
}
