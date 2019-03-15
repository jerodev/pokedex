<?php

namespace App\Repositories;

use DateTime;
use Illuminate\Database\Query\Builder as QueryBuilder;
use stdClass;

class FactRepository extends Repository
{
    /** @var string */
    private const table = 'facts';

    /** @var ChannelRepository */
    private $channelRepository;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(ChannelRepository $channelRepository, UserRepository $userRepository)
    {
        $this->channelRepository = $channelRepository;
        $this->userRepository = $userRepository;
    }

    public function getLastUserFact(string $user, string $channel, ?int $minutesBack): ?stdClass
    {
        $query = $this->factQuery()
            ->whereExists(function ($query) use ($channel) {
                $query->from('channels')
                    ->whereColumn('channels.id', '=', 'facts.channel_id')
                    ->where('channels.name', $channel)
                    ->select($this->raw(1));
            })
            ->whereExists(function ($query) use ($user) {
                $query->from('users')
                    ->leftJoin('aliases', 'aliases.user_id', '=', 'users.id')
                    ->whereColumn('users.id', '=', 'facts.user_id')
                    ->where(function ($query) use ($user) {
                        $query
                            ->orWhere('users.nickname', $user)
                            ->orWhere('aliases.nickname', $user);
                    })
                    ->select($this->raw(1));
            });

        if ($minutesBack) {
            $query = $query->where('created_at', '>', $this->raw("(now() - interval $minutesBack minute)"));
        }

        return $query->latest()->first();
    }

    public function getResponseString(string $command, string $channel, bool $updateUseCount = false): ?string
    {
        $fact = $this->getCommandQuery($command, $channel)
            ->inRandomOrder()
            ->select('id', 'response')
            ->first();

        if ($fact) {
            $this->factQuery()->where('id', $fact->id)->increment('uses');

            return $fact->response;
        }

        return null;
    }

    public function getSingleStats(string $command, string $channel)
    {
        return $this->getCommandQuery($command, $channel)
            ->groupBy('command')
            ->select(
                $this->raw('MIN(created_at) as created_at'),
                $this->raw('SUM(uses) as uses'),
                $this->raw('COUNT(1) as response_count')
            )
            ->first();
    }

    public function getStats(string $channel)
    {
        $fact = $this->factQuery()
            ->join('users', 'users.id', '=', self::table.'.user_id')
            ->whereExists(function ($query) use ($channel) {
                $query->from('channels')
                    ->whereColumn('channels.id', '=', 'facts.channel_id')
                    ->where('channels.name', $channel)
                    ->select($this->raw(1));
            })
            ->select(
                'created_at',
                'users.nickname',
                'command'
            )
            ->orderByDesc('created_at')
            ->first();

        $fact->fact_count = parent::query(self::table)->whereExists(function ($query) use ($channel) {
            $query->from('channels')
                ->whereColumn('channels.id', '=', 'facts.channel_id')
                ->where('channels.name', $channel)
                ->select($this->raw(1));
        })->count();

        return $fact;
    }

    public function learnFact(string $nickname, string $channel, string $command, string $response): void
    {
        $channelid = $this->channelRepository->getChannelId($channel);
        $userid = $this->userRepository->getUserId($nickname);

        parent::query(self::table)->insert([
            'channel_id' => $channelid,
            'user_id'    => $userid,
            'command'    => $command,
            'response'   => $response,
        ]);
    }

    public function removeFact(int $id): void
    {
        $this->factQuery()->where('id', $id)->update(['deleted_at' => new DateTime()]);
    }

    /**
     *  Create a query builder that filters on command and channel.
     *
     *  @param string $command
     *  @param string $channel
     *
     *  @return QueryBuilder
     */
    private function getCommandQuery(string $command, string $channel): QueryBuilder
    {
        return $this->factQuery()
            ->where('command', $command)
            ->whereExists(function ($query) use ($channel) {
                $query->from('channels')
                    ->whereColumn('channels.id', '=', 'facts.channel_id')
                    ->where('channels.name', $channel)
                    ->select($this->raw(1));
            });
    }

    private function factQuery(): QueryBuilder
    {
        return parent::query(self::table)->whereNull('deleted_at');
    }
}
