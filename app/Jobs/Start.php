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
        $hour = config('santa.start.hour');
        $day = config('santa.start.day');
        $month = config('santa.start.month');

        if ($hour == null || $day == null || $month == null) {
            return;
        }

        $now = Carbon::now();
        $isSameDay = $now->isSameDay(Carbon::createFromDate($now->year, $month, $day));
        if (!$isSameDay || $now->hour != $hour || $now->minute !== 0) {
            return;
        }

        $state = State::byName('bot');
        if ($state == State::STARTED || $state == State::DRAWING || $state == State::IDLE) {
            return;
        }

        $channelId = State::byName('announcement_channel');
        if ($channelId == null) {
            return;
        }

        State::set('bot', State::STARTED);

        $hour = config('santa.end_participation.hour');
        $day = config('santa.end_participation.day');
        $month = config('santa.end_participation.month');
        $year = Carbon::now()->year;

        $dateString = Carbon::create($year, $month, $day, $hour)
            ->formatLocalized('%e. %B %G um %k Uhr');

        $service->send($channelId, Stub::load('start.message', [
            'date' => $dateString
        ]), function (MessageHandler $message) {
            State::set('announcement_id', $message->getId());
        });
    }
}
