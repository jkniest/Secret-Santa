<?php

namespace App\Jobs;

use App\Discord\MessageService;
use App\Models\Participant;
use App\Models\State;
use App\Stub;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This job will send a new announcement post that the presents should now be given to the
 * partners. Also a DM is sent to every participant.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class GivePresents
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
        $month = config('santa.give.month');
        $day = config('santa.give.day');
        $hour = config('santa.give.hour');

        if ($month == null || $day == null || $hour == null) {
            return;
        }

        $now = Carbon::now();
        $isSameDay = $now->isSameDay(Carbon::createFromDate($now->year, $month, $day));
        if (!$isSameDay || $now->hour != $hour || $now->minute !== 0) {
            return;
        }

        $state = State::byName('bot');
        if ($state == State::STARTED || $state == State::STOPPED || $state == State::IDLE) {
            return;
        }

        $channelId = State::byName('announcement_channel');

        $service->delete(
            State::byName('announcement_id'),
            $channelId
        );

        $service->send($channelId, Stub::load('presents.message'));

        Participant::all()->each(function ($participant) use ($service) {
            $service->sendDm(
                $participant->discord_user_id,
                'Heute beginnt das Schenken! Bitte denk dran :-)'
            );
        });
    }
}
