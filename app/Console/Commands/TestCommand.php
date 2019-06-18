<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Jerodev\PhpIrcClient\IrcChannel;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test {responder} {payload}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test a certain command';

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
        // Create responder instance
        $responderClass = '\App\Irc\Responders\\' . $this->argument('responder');
        if (!class_exists($responderClass)) {
            throw new Exception("Class $responderClass does not exist!");
        }
        $responder = new $responderClass();

        $response = $responder->handlePrivmsg('foo', new IrcChannel('#pokedextest'), $this->argument('payload'), true);
        if ($response) {
            $this->line($response->getMessage());
        }
    }
}
