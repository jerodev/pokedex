<?php

namespace App\Console\Commands;

use App\Irc\IrcBot;
use App\Irc\Responders\FactResponder;
use App\Irc\Responders\GiphyResponder;
use App\Irc\Responders\JokeResponder;
use App\Irc\Responders\NewsResponder;
use App\Irc\Responders\Logger;
use App\Irc\Responders\QuestionResponderEN;
use App\Irc\Responders\QuestionResponderNL;
use App\Irc\Responders\TimeResponder;
use Illuminate\Console\Command;

class Pokedex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pokedex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the pokedex irc bot';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = env('IRC_SERVER', 'euroserv.fr.quakenet.org:6667');
        $botName = env('IRC_BOTNAME', 'PokedexTest');
        $channels = explode(',', env('IRC_CHANNELS', '#pokedextest'));

        $bot = new IrcBot($server, $botName, $channels);
        $bot->addResponder($channels, new GiphyResponder());
        $bot->addResponder($channels, new JokeResponder());
        $bot->addResponder($channels, new NewsResponder());
        $bot->addResponder($channels, new TimeResponder());
        $bot->addResponder($channels, new QuestionResponderEN());
        $bot->addResponder($channels, new QuestionResponderNL());
        $bot->addResponder($channels, new FactResponder());
        $bot->addResponder($channels, new Logger());
        $bot->connect();
    }
}
