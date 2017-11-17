<?php

namespace App\Console\Commands;

use App\Discord\Commands\CommandHandler;
use App\Discord\Commands\DefaultCommand;
use App\Discord\Commands\ListCommand;
use App\Discord\Commands\MarkCommand;
use App\Discord\DiscordMessage;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Illuminate\Console\Command;

/**
 * This artisan command starts and runs the discord bot. It should be executed
 * as a background task / daemon.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
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
     * @var CommandHandler
     */
    private $commandHandler;

    /**
     * RunCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandHandler = new CommandHandler('!santa', [
            ''     => DefaultCommand::class,
            'list' => ListCommand::class,
            'mark' => MarkCommand::class
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $bot = new Discord([
            'token' => config('services.discord.token')
        ]);

        $bot->on('ready', function (Discord $discord) {
            $discord->on('message', function (Message $message) {
                $this->commandHandler->handle(new DiscordMessage($message));
            });
        });

        $bot->run();
    }
}
