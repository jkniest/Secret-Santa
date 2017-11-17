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
        State::set('bot', State::STARTED);

        $hour = config('santa.end_participation.hour');
        $day = config('santa.end_participation.day');
        $month = config('santa.end_participation.month');
        $year = Carbon::now()->year;

        $dateString = Carbon::create($year, $month, $day, $hour)
            ->formatLocalized('%e. %B %G um %k Uhr');

        $channelId = State::byName('announcement_channel');
        $service->send($channelId, Stub::load('start.message', [
            'date' => $dateString
        ]), function (MessageHandler $message) {
            State::set('announcement_id', $message->getId());
        });
    }
}
