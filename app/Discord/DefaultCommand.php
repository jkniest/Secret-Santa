<?php

namespace App\Discord;

use App\Models\Participant;
use Discord\Parts\Channel\Message;
use Tests\Fakes\FakeMessage;

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
     * @var Message|FakeMessage
     */
    private $message;

    /**
     * DefaultCommand constructor.
     *
     * @param Message|FakeMessage $message The incoming message
     */
    public function __construct($message)
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
        Participant::create([
            'discord_user_id' => $this->message->author->id
        ]);

        $this->message->channel->messages->delete($this->message);

        $this->message->reply('du bist nun für das Wichtelspiel eingetragen.');

        $this->message->author->user->sendMessage(
            $this->loadStub(__DIR__ . '/../Stubs/welcome.message')
        );
    }

    /**
     * Load a given stub file and replace the {{username}} variable with the
     * actual username of the author.
     *
     * @param string $path The stub path
     *
     * @return mixed
     */
    private function loadStub(string $path)
    {
        return str_replace(
            '{{username}}',
            $this->message->author->username,
            file_get_contents($path)
        );
    }
}
