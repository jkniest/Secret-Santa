<?php

namespace App\Jobs;

use App\Discord\MessageHandler;
use App\Discord\MessageService;
use App\Models\Participant;
use App\Models\State;
use App\Stub;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * This job will give a partner to all participants. Also a new announcement post will be
 * made.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class Draw
{
    use Dispatchable, SerializesModels;

    /**
     * Execute the job.
     *
     * @param MessageService $service The message service
     *
     * @return void
     */
    public function handle(MessageService $service)
    {
        $participants = Participant::all();

        if (!$this->validate($participants)) {
            return;
        }

        $this->assignRandomPartners($participants);
        $this->sendDirectMessages($service, $participants);
        $this->sendAnnouncement($service);
    }

    /**
     * Validate if all requirements are given.
     *
     * 1.) The date and time must be correct
     * 2.) The state must be set to drawing
     * 3.) The participant count must be at least 2
     *
     * @param Collection $participants All participants of the game
     *
     * @return bool
     */
    private function validate(Collection $participants)
    {
        if (!$this->validateDate()) {
            return false;
        }

        if (State::byName('bot') != State::DRAWING) {
            return false;
        }

        if ($participants->count() <= 1) {
            return false;
        }

        return true;
    }

    /**
     * Validate if the given date and hour. If the date is equals to the configured
     * draw date, and if the hour is also correct, this menthod will return true.
     *
     * @return bool
     */
    private function validateDate()
    {
        $month = config('santa.draw.month');
        $day = config('santa.draw.day');
        $hour = config('santa.draw.hour');

        if ($month == null || $day == null || $hour == null) {
            return false;
        }

        $now = Carbon::now();
        $isSameDay = $now->isSameDay(Carbon::createFromDate($now->year, $month, $day));
        if (!$isSameDay || $now->hour != $hour || $now->minute !== 0) {
            return false;
        }

        return true;
    }

    /**
     * Generate a new ID with shuffeld discord user id's. This method also assures,
     * that no id will be on the same location as the original id.
     *
     * @param Collection $participants All participants of the game
     *
     * @return array
     */
    private function shuffleIds(Collection $participants)
    {
        $original = $participants->map->discord_user_id->toArray();
        $new = $original;

        $remainingTries = 1000;
        while (count(array_intersect_assoc($original, $new)) && $remainingTries > 0) {
            shuffle($new);
            $remainingTries--;
        }

        return $new;
    }

    /**
     * Assign every participant a random partner.
     * See also: shuffleIds()
     *
     * @param Collection $participants All participants of the game
     *
     * @return void
     */
    private function assignRandomPartners(Collection $participants)
    {
        $newIds = $this->shuffleIds($participants);

        $participants->each(function (Participant $participant, $i) use ($newIds) {
            $participant->update(['partner_id' => $newIds[$i]]);
        });
    }

    /**
     * Send a direct message to every participant with their partner.
     *
     * @param MessageService $service      The messaging service
     * @param Collection     $participants All participants of the game
     *
     * @return void
     */
    private function sendDirectMessages(MessageService $service, Collection $participants)
    {
        $hour = config('santa.give.hour');
        $day = config('santa.give.day');
        $month = config('santa.give.month');
        $year = Carbon::now()->year;

        $dateString = Carbon::create($year, $month, $day, $hour)
            ->formatLocalized('%e. %B %G');

        $participants->each(function ($participant) use ($service, $dateString) {
            $service->sendDm($participant->discord_user_id, Stub::load(
                'partner.message',
                [
                    'id'   => $participant->partner_id,
                    'date' => $dateString
                ]
            ));
        });
    }

    /**
     * Delete the old announcement post and send a new one.
     *
     * @param MessageService $service The messaging service
     *
     * @return void
     */
    private function sendAnnouncement(MessageService $service)
    {
        $service->delete(State::byName('announcement_id'), State::byName('announcement_channel'));

        $hour = config('santa.give.hour');
        $day = config('santa.give.day');
        $month = config('santa.give.month');
        $year = Carbon::now()->year;

        $dateString = Carbon::create($year, $month, $day, $hour)
            ->formatLocalized('%e. %B %G');

        $service->send(
            State::byName('announcement_channel'),
            Stub::load('draw-done.message', [
                'date' => $dateString
            ]),
            function (MessageHandler $message) {
                State::set('announcement_id', $message->getId());
            }
        );
    }
}
