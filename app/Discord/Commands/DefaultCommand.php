<?php

namespace App\Discord\Commands;

use App\Discord\MessageHandler;
use App\Models\Participant;
use App\Stub;

/**
 * This command is the default '!santa' command. It will register a new participant.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class DefaultCommand
{
    /**
     * @var MessageHandler
     */
    private $message;

    /**
     * DefaultCommand constructor.
     *
     * @param MessageHandler $message The incoming message
     */
    public function __construct(MessageHandler $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the command.
     *
     * 1.) Save the user as a new participant
     * 2.) Delete the command message of the user
     * 3.) Reply with a generic sentence
     * 4.) Send a DM to the user with detailed information
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->message->getAuthor()->getId();
        if (Participant::where('discord_user_id', $id)->count() === 0) {
            Participant::create([
                'discord_user_id' => $id
            ]);

            $this->message->delete()
                ->reply('du bist nun für das Wichtelspiel eingetragen.')
                ->sendDm(Stub::load('welcome.message', [
                    'username' => $this->message->getAuthor()->getUsername()
                ]));
        } else {
            Participant::where('discord_user_id', $id)->delete();

            $this->message->delete()
                ->reply('du bist nun für das Wichtelspiel ausgetragen. Schade :(');
        }
    }
}
