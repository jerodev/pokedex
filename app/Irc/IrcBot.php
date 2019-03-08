<?php

namespace App\Irc;

use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\Options\ClientOptions;
use App\Irc\Responders\Responder;

class IrcBot
{
    /** @var Channel[] */
    private $channels;

    /** @var IrcClient */
    private $client;

    /**
     *  Create a new bot instance
     *
     *  @param string $server The server address and port of the irc server
     *  @param string $username The username for the bot
     *  @param string[] $channels IRC channels to join on connect
     */
    public function __construct(string $server, string $username, array $channels = [])
    {
        $this->channels = [];
        foreach ($channels as $channel) {
            $this->channels[$channel] = new Channel($channel);
        }

        $options = new ClientOptions($username, $channels);
        $options->floodProtectionDelay = 750;

        $this->client = new IrcClient($server, $options);
        $this->client->on('message', function () {
            $this->privmsg(...func_get_args());
        });
    }

    /**
     *  Register a responder for one or more channels.
     *
     *  @param string|string[] $channel The channel(s) to register this responder for.
     *  @param Responder $responder The responder to execute.
     */
    public function addResponder($channels, Responder $responder): void
    {
        if (!is_array($channels)) {
            $channels = [$channels];
        }

        foreach ($channels as $channel) {
            if (!array_key_exists($channel, $this->channels)) {
                continue;
            }

            $this->channels[$channel]->addResponder($responder);
        }
    }

    /**
     *  Open the irc connection
     */
    public function connect(): void
    {
        $this->client->connect();
    }

    /**
     *  Handle a new private or channel message.
     *
     *  @param string $from The nickname of the user who sent the message.
     *  @param string $to The destination of the message. If this is a channel, the string will start with `#`.
     *  @param string $message The message itself.
     */
    private function privmsg(string $from, string $to, string $message): void
    {
        $channel = $this->channels[$to] ?? null;
        if ($channel === null) {
            return;
        }

        $response = $channel->handlePrivmsg($from, $message);
        if ($response !== null) {
            $this->client->say($to, $response);
        }
    }
}