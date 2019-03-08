<?php

namespace App\Console\Commands;

use App\Irc\IrcBot;
use App\Irc\Responders\FactResponder;
use App\Irc\Responders\Logger;
use App\Irc\Responders\TimeResponder;
use App\Irc\Responders\QuestionResponderEN;
use App\Irc\Responders\QuestionResponderNL;
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
        $bot = new IrcBot('euroserv.fr.quakenet.org:6667', 'Pokedex', ['#pokedextest']);
        $bot->addResponder('#pokedextest', new TimeResponder());
        $bot->addResponder('#pokedextest', new QuestionResponderEN());
        $bot->addResponder('#pokedextest', new QuestionResponderNL());
        $bot->addResponder('#pokedextest', new FactResponder());
        $bot->addResponder('#pokedextest', new Logger());
        $bot->connect();
    }
}