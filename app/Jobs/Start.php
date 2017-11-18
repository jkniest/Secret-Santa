<?php

namespace App\Jobs;

use App\Discord\MessageHandler;
use App\Discord\MessageService;
use App\Models\State;
use App\Stub;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This job starts the bot at a given date and time if the announcement channel was marked before.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class Start
{
    use Dispatchable, SerializesModels;

    /**
     * Execute the job.
     *
     * @param MessageService $service The messaging service
     *
     * @return void
     */
    public function handle(MessageService $service)
    {
        $channelId = State::byName('announcement_channel');

        if (!$this->validate($channelId)) {
            return;
        }

        State::set('bot', State::STARTED);

        $service->send($channelId, Stub::load('start.message', [
            'date' => $this->getDateString()
        ]), function (MessageHandler $message) {
            State::set('announcement_id', $message->getId());
        });
    }

    /**
     * Validate that the state, the date and the channel id are all valid.
     *
     * @param string|null $channelId The announcement channel id
     *
     * @return bool
     */
    private function validate($channelId)
    {
        $validDate = $this->validateDate();
        $state = State::byName('bot');

        return $state == State::STOPPED && $validDate && $channelId != null;
    }

    /**
     * Validate the a start date is given and that this date and time are now.
     *
     * @return bool
     */
    private function validateDate()
    {
        $hour = config('santa.start.hour');
        $day = config('santa.start.day');
        $month = config('santa.start.month');

        if ($hour == null || $day == null || $month == null) {
            return false;
        }

        $now = Carbon::now();
        $isSameDay = $now->isSameDay(Carbon::createFromDate($now->year, $month, $day));

        return $isSameDay && $now->hour == $hour && $now->minute == 0;
    }

    /**
     * Convert the start date to a human-readable version.
     *
     * Format: 17. December 2017 um 15 Uhr
     *
     * @return string
     */
    private function getDateString()
    {
        $hour = config('santa.end_participation.hour');
        $day = config('santa.end_participation.day');
        $month = config('santa.end_participation.month');
        $year = Carbon::now()->year;

        return Carbon::create($year, $month, $day, $hour)
            ->formatLocalized('%e. %B %G um %k Uhr');
    }
}
