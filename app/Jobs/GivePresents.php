<?php

namespace App\Jobs;

use App\Discord\MessageService;
use App\Models\Participant;
use App\Models\State;
use App\Stub;
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
