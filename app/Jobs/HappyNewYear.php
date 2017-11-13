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
 * This job will simply post a happy new year message to the announcement channel. Also the
 * participant list will be cleared.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class HappyNewYear
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

        $channelId = State::byName('announcement_channel');

        $service->delete(State::byName('announcement_id'), $channelId);

        $service->send($channelId, Stub::load('new-year.message'));

        State::set('bot', State::IDLE);

        Participant::all()->each->delete();
    }

    /**
     * Validating if the current date is the new year and the state is set to drawing.
     *
     * @return bool
     */
    private function validate()
    {
        $now = Carbon::now();

        if ($now->day != 1 || $now->month != 1 || $now->hour != 0 || $now->minute != 0) {
            return false;
        }

        if (State::byName('bot') != State::DRAWING) {
            return false;
        }

        return true;
    }
}
