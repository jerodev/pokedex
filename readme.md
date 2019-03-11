# Pokedex IRC bot
[![Build Status](https://travis-ci.com/jerodev/pokedex.svg?branch=master)](https://travis-ci.com/jerodev/pokedex) [![StyleCI](https://github.styleci.io/repos/174622519/shield?branch=master)](https://github.styleci.io/repos/174622519)

Pokedex is a simple modular IRC bot using my [PHP IRC Client](https://github.com/jerodev/php-irc-client).

The project is built on top of a heavily stripped down version of [the Lumen framework](https://lumen.laravel.com/).

## Installation
To use the bot: simply [clone the repository](#clone-the-repository), [update configuration](#configure) and [run the `pokedex` command](#start-the-bot).

### Clone the repository

``` bash
# Clone Repository
git clone git@github.com:jerodev/pokedex.git

# Install composer packages
composer install
```

### Configure
Configuration can be done using an `.env` file in the root of the project. It should contain these configurations:

``` yaml
# Irc bot configuration
IRC_BOTNAME=BotName
IRC_CHANNELS="#channel1,#channel2" # The quotes are required!

# Used Fact & Logger responders
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=database
DB_USERNAME=username
DB_PASSWORD=password

# Used for the Giphy responder
GIPHY_API=api-key
```

### Start the bot.
In the root of the application, run this command:

``` bash
php artisan pokedex
```
