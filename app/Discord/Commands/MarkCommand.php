<?php

namespace App\Discord\Commands;

use App\Discord\MessageHandler;
use App\Models\State;

/**
 * This command is used to save the channel id of the announcement channel. It is one-time use
 * only.. and only if the bot has not started yet.
 *
 * If the channel should be changed later, the artisan command "reset:channel" should be executed.
 * After that this command will be useable again for one use.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class MarkCommand
{
    /**
     * @var MessageHandler
     */
    private $message;

    /**
     * MarkCommand constructor.
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
     * 1.) Set the announcement channel id
     * 2.) Delete the command message
     *
     * @return void
     */
    public function handle()
    {
        if (State::byName('announcement_channel') != null) {
            return;
        }

        State::set('announcement_channel', $this->message->getChannelId());

        $this->message->delete();
    }
}
