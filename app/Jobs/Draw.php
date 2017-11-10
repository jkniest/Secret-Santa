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
        $month = config('santa.draw.month');
        $day = config('santa.draw.day');
        $hour = config('santa.draw.hour');

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

        $participants = Participant::all();

        if ($participants->count() === 0 || $participants->count() === 1) {
            return;
        }

        // Select random partners
        $allIds = $participants->map->discord_user_id->toArray();
        $newIds = $allIds;

        $max = 1000;
        while (count(array_intersect_assoc($allIds, $newIds)) && $max > 0) {
            shuffle($newIds);
            $max--;
        }

        for ($i = 0; $i < $participants->count(); $i++) {
            $participants[$i]->update(['partner_id' => $newIds[$i]]);
        }

        // Send a DM to every single user
        $participants->each(function ($participant) use ($service) {
            $service->sendDm($participant->discord_user_id, Stub::load(
                'partner.message',
                ['id' => $participant->partner_id]
            ));
        });

        // Delete old announcement post
        $service->delete(State::byName('announcement_id'), State::byName('announcement_channel'));

        $service->send(State::byName('announcement_channel'), Stub::load(
            'draw-done.message'
        ), function (MessageHandler $message) {
            State::set('announcement_id', $message->getId());
        });
    }
}
