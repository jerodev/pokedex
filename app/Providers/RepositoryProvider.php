<?php

namespace App\Providers;

use App\Repositories\ChannelRepository;
use App\Repositories\FactRepository;
use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ChannelRepository::class);
        $this->app->singleton(FactRepository::class);
        $this->app->singleton(MessageRepository::class);
        $this->app->singleton(UserRepository::class);
    }
}