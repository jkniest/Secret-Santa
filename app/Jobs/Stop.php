<?php

namespace App\Jobs;

use App\Discord\MessageService;
use App\Models\State;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Stop the bot for this year. It will simply remove the announcement message and change
 * the state to stopped.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class Stop
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
        $service->delete(
            State::byName('announcement_id'),
            State::byName('announcement_channel')
        );

        State::set('announcement_id', null);
    }
}
