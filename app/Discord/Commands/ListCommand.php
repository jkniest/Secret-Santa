<?php

namespace App\Discord\Commands;

use App\Discord\MessageHandler;
use App\Models\Participant;
use App\Stub;
use Illuminate\Support\Collection;

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

        $participants = $this->getParticipants();

        $this->message->reply($this->getMessage($participants));
    }

    /**
     * Load all participants from the database and transform them into a simple string
     * array with all their id's.
     *
     * @return Collection
     */
    protected function getParticipants()
    {
        return Participant::select('discord_user_id')
            ->get()
            ->pluck('discord_user_id')
            ->map(function ($id) {
                return "- <@{$id}>";
            });
    }

    /**
     * Get the message which should be send as a reply to the user. If no participants
     * exists, a generic message will be send. Otherwise a list of all participants.
     *
     * @param Collection $participants All participants of the game
     *
     * @return string
     */
    protected function getMessage(Collection $participants)
    {
        if ($participants->count() === 0) {
            return 'Leider nehmen noch keine Personen an dem Spiel teil.';
        }

        return Stub::load('list.message', [
            'participants' => $participants->implode("\n")
        ]);
    }
}
