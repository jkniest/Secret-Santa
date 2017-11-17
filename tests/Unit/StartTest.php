<?php

namespace Tests\Unit;

use App\Discord\MessageService;
use App\Jobs\Start;
use App\Models\State;
use Carbon\Carbon;
use Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMessageService;
use Tests\TestCase;

class StartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_start_the_bot_at_a_given_date()
    {
        // Given: The configured start date is the 3rd december at 4pm
        Config::set('santa.start.hour', 16);
        Config::set('santa.start.day', 3);
        Config::set('santa.start.month', 12);

        // Given: The participation end is set to the the 4th december at 1pm
        Config::set('santa.end_participation.day', 4);
        Config::set('santa.end_participation.month', 12);
        Config::set('santa.end_participation.hour', 13);

        // Given: The current date and time are the 3rd december at 4pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 3, 16));

        // Given: The announcement channel is set to '12345'
        State::set('announcement_channel', 12345);

        // Given: A faked messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // When: We execute the command
        dispatch(new Start());

        // Then: The bot state should have changed to "STARTED"
        $this->assertEquals(State::STARTED, State::byName('bot'));

        // And: A start message should have been send to the announcement channel
        $year = Carbon::now()->year;
        $this->assertEquals('12345', $service->channelId);
        $this->assertContains(" 4. Dezember {$year} um 13 Uhr", $service->message);

        // And: The announcement post id should have been saved
        $this->assertEquals('1234', State::byName('announcement_id'));
    }

    // TODO: Validate date
    // TODO: Validate state (== stopped)
    // TODO: Validate announcement_channel state (not null)
    // TODO: Remove "santa start" command
    // TODO: Implement "santa mark" command
}
