<?php

namespace App\Discord\Commands;

use App\Discord\MessageHandler;
use App\Models\Participant;
use App\Stub;

/**
 * List all participants.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class ListCommand
{
    /**
     * @var MessageHandler
     */
    private $message;

    /**
     * ListCommand constructor.
     *
     * @param MessageHandler $message The incoming message
     */
    public function __construct(MessageHandler $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the command:
     *
     * - Delete the command message
     * - Reply with all participants, if existant
     * - Or reply with a generic message if no participants exists
     *
     * @return void
     */
    public function handle()
    {
        $this->message->delete();

        $participants = Participant::select('discord_user_id')
            ->get()
            ->pluck('discord_user_id')
            ->map(function ($id) {
                return "- <@{$id}>";
            });

        if ($participants->count() === 0) {
            $this->message->reply('Leider nehmen noch keine Personen an dem Spiel teil.');
        } else {
            $this->message->reply(Stub::load('list.message', [
                'participants' => $participants->implode("\n")
            ]));
        }
    }
}
