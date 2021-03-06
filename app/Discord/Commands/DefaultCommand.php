<?php

namespace App\Discord\Commands;

use App\Discord\MessageHandler;
use App\Models\Participant;
use App\Models\State;
use App\Stub;
use Carbon\Carbon;

/**
 * This command is the default '!santa' command. It will register a new participant or
 * remove a existing one.
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
     * Execute the command. If the bot is not started yet, don't do anything.
     *
     * 1.) Delete the command message
     * 2.) Check if the user alreay, participates in the game
     * 2.1.) If so, remove the user from the game
     * 2.2.) If not, add a new participation
     *
     * @return void
     */
    public function handle()
    {
        if (State::byName('bot') != State::STARTED) {
            return;
        }

        $id = $this->message->getAuthor()->getId();
        $participant = Participant::where('discord_user_id', $id)->first();

        $this->message->delete();

        if ($participant == null) {
            $this->addParticipant($id);
        } else {
            $this->removeParticipant($id);
        }
    }

    /**
     * Add a new user to the game.
     *
     * 1.) Create the participation
     * 2.) Reply to the user
     * 3.) Send a DM with detailed information to the user
     *
     * @param string $id The discord user id
     *
     * @return void
     */
    protected function addParticipant(string $id)
    {
        Participant::create([
            'discord_user_id' => $id
        ]);

        $this->message->reply('du bist nun für das Wichtelspiel eingetragen.');

        $this->message->sendDm(Stub::load('welcome.message', [
            'username' => $this->message->getAuthor()->getUsername(),
            'drawDate' => $this->getDrawDateString(),
            'giveDate' => $this->getGiveDateString()
        ]));
    }

    /**
     * Remove a user from the game.
     *
     * 1.) Remove the participation
     * 2.) Reply to the user
     *
     * @param string $id The discord user id
     *
     * @return void
     */
    protected function removeParticipant(string $id)
    {
        Participant::where('discord_user_id', $id)->delete();
        $this->message->reply('du bist nun für das Wichtelspiel ausgetragen. Schade :(');
    }

    /**
     * Generate a human-readable string of the date when the drawing is done.
     *
     * Format: 1. December 2017 um 14 Uhr
     *
     * @return string
     */
    private function getDrawDateString()
    {
        $hour = config('santa.draw.hour');
        $day = config('santa.draw.day');
        $month = config('santa.draw.month');
        $year = Carbon::now()->year;

        return Carbon::create($year, $month, $day, $hour)
            ->formatLocalized('%e. %B %G um %k Uhr');
    }

    /**
     * Generate a human-readable string of the date when the presents should be given.
     *
     * Format: 1. Decemer 2017 um 14 Uhr
     *
     * @return string
     */
    protected function getGiveDateString()
    {
        $hour = config('santa.give.hour');
        $day = config('santa.give.day');
        $month = config('santa.give.month');
        $year = Carbon::now()->year;

        return Carbon::create($year, $month, $day, $hour)
            ->formatLocalized('%e. %B %G um %k Uhr');
    }
}
