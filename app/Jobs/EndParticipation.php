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
 * This job will cancel the participation phase.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class EndParticipation
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
        $day = config('santa.end_participation.day');
        $month = config('santa.end_participation.month');
        $hour = config('santa.end_participation.hour');

        if ($day == null || $month == null || $hour == null) {
            return;
        }

        $now = Carbon::now();
        $isSameDay = $now->isSameDay(Carbon::createFromDate($now->year, $month, $day));
        if (!$isSameDay || $now->hour != $hour || $now->minute !== 0) {
            return;
        }

        $state = State::byName('bot');
        if ($state == State::DRAWING || $state == State::IDLE || $state == State::STOPPED) {
            return;
        }

        State::set('bot', State::DRAWING);

        $service->delete(State::byName('announcement_id'), State::byName('announcement_channel'));

        $channel = State::byName('announcement_channel');
        $service->send($channel, Stub::load('draw.message'), function (MessageHandler $message) {
            State::set('announcement_id', $message->getId());
        });
    }
}
