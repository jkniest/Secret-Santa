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
        if (!$this->validate()) {
            return;
        }

        $this->sendAnnouncementMessage($service);
        $this->sendDirectMessages($service);
    }

    /**
     * Validate if the date and state are correct.
     *
     * @return bool
     */
    private function validate()
    {
        return $this->validateDate() && State::byName('bot') == State::DRAWING;
    }

    /**
     * Validate that the date and time are correct.
     *
     * @return bool
     */
    private function validateDate()
    {
        $month = config('santa.give.month');
        $day = config('santa.give.day');
        $hour = config('santa.give.hour');

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
     * Delete the old announcement post and create a new one.
     *
     * @param MessageService $service The messaging service
     *
     * @return void
     */
    private function sendAnnouncementMessage(MessageService $service)
    {
        $channelId = State::byName('announcement_channel');

        $service->delete(
            State::byName('announcement_id'),
            $channelId
        );

        $service->send($channelId, Stub::load('presents.message'));
    }

    /**
     * Send a small direct message to all participants with the information that the
     * giving starts today.
     *
     * @param MessageService $service The messaging service
     *
     * @return void
     */
    private function sendDirectMessages(MessageService $service)
    {
        Participant::all()->each(function ($participant) use ($service) {
            $service->sendDm(
                $participant->discord_user_id,
                'Heute beginnt das Schenken! Bitte denk dran :-)'
            );
        });
    }
}
