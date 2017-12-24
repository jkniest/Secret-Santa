<?php

namespace App\Console\Commands;

use App\Discord\MessageService;
use App\Models\Participant;
use App\Stub;
use Illuminate\Console\Command;
use RestCord\DiscordClient;

class WorkAroundCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workaround';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simple Workaround command';

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
     * @return void
     */
    public function handle()
    {
        $client = new DiscordClient(['token' => config('services.discord.token')]);
        $service = app(MessageService::class);

        Participant::all()->each(function ($participant) use ($service, $client) {
            $username = $client->user->getUser([
                'user.id' => intval($participant->partner_id),
            ])->username;

            $service->sendDm($participant->discord_user_id, Stub::load(
                'bug.message',
                [
                    'name' => $username,
                ]
            ));
        });
    }
}
