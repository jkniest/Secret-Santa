<?php

namespace App\Console\Commands;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Illuminate\Console\Command;

class RunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start and run the discord bot';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bot = new Discord([
            'token' => config('services.discord.token')
        ]);

        $bot->on('ready', function (Discord $discord) {

            // Handle messages and commands
            $discord->on('message', function (Message $message) {
                if ($message->content === '!santa') {
                    $message->reply('Hi');
                }
            });

        });

        $bot->run();
    }
}
