<?php

namespace App\Discord\Commands;

use App\Discord\MessageHandler;
use App\Models\State;
use App\Stub;

/**
 * This command is used to start the discord bot.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class StartCommand
{
    /**
     * @var MessageHandler
     */
    private $message;

    /**
     * StartCommand constructor.
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
     * 1.) Set the bot state to started
     * 2.) Delete the command message
     * 3.) Make a static reply with generic information about the game
     * 3.1.) Save the id of the message
     *
     * @return void
     */
    public function handle()
    {
        if (State::byName('bot') != State::STOPPED) {
            return;
        }

        State::set('bot', State::STARTED);

        $this->message->delete();

        $content = Stub::load('start.message');
        $this->message->staticReply($content, function (MessageHandler $message) {
            State::set('announcement_id', $message->getId());
            State::set('announcement_channel', $message->getChannelId());
        });
    }
}
