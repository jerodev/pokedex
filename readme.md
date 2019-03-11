# Pokedex IRC bot
Pokedex is a simple modular IRC bot using my [PHP IRC Client](https://github.com/jerodev/php-irc-client).

The project is built on top of a heavily stripped down version of [the Lumen framework](https://lumen.laravel.com/).

## Installation
To use the bot, simply clone this repository and run the following command:

    php artisan pokedex

## Customization
The irc server, channels and responders can be customized in  [`app/Console/Commands/Pokedex.php`](app/Console/Commands/Pokedex.php#L46).