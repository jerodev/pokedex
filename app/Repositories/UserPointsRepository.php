<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UserPointsRepository extends Repository
{
    /** @var string */
    private const table = 'user_point_votes';

    /** @var ChannelRepository */
    private $channelRepository;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(ChannelRepository $channelRepository, UserRepository $userRepository)
    {
        $this->channelRepository = $channelRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Record a single vote.
     */
    public function castVote(string $from, string $to, string $channel, bool $is_upvote)
    {
        $channelId = $this->channelRepository->getChannelId($channel);
        $fromId = $this->userRepository->getUserId($from);
        $toId = $this->userRepository->getUserId($to, false);

        if ($toId === null) {
            return;
        }

        parent::query(self::table)->insert([
            'channel_id' => $channelId,
            'user_id' => $toId,
            'voter_id' => $fromId,
            'is_upvote' => $is_upvote,
        ]);
    }

    /**
     * Count the number of votes from a user.
     */
    public function countUserVotes(string $nickname, bool $todayOnly = false)
    {
        $userId = $this->userRepository->getUserId($nickname);

        return parent::query(self::table)
            ->where('voter_id', $userId)
            ->when($todayOnly, function ($query) {
                $query->whereRaw('DATE(created_at) = CURDATE()');
            })
            ->count();
    }

    /**
     *  Get the karma score for a single user.
     */
    public function getUserScore(string $nickname, string $channel): int
    {
        $channelId = $this->channelRepository->getChannelId($channel);
        $userId = $this->userRepository->getUserId($nickname);

        return parent::query(self::table)
            ->where('user_id', $userId)
            ->where('channel_id', $channelId)
            ->groupBy('user_id')
            ->sum(DB::raw('CASE is_upvote WHEN 0 THEN -1 ELSE 1 END'));
    }
}
