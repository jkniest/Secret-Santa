<?php

namespace App\Jobs;

use App\Discord\MessageHandler;
use App\Discord\MessageService;
use App\Models\State;
use App\Stub;
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
        State::set('bot', State::DRAWING);

        $service->delete(State::byName('announcement_id'), State::byName('announcement_channel'));

        $channel = State::byName('announcement_channel');
        $service->send($channel, Stub::load('draw.message'), function (MessageHandler $message) {
            State::set('announcement_id', $message->getId());
        });
    }
}
