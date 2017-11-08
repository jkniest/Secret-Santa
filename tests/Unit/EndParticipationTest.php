<?php

namespace Tests\Unit;

use App\Discord\MessageService;
use App\Jobs\EndParticipation;
use App\Models\State;
use Carbon\Carbon;
use Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMessageService;
use Tests\TestCase;

class EndParticipationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_stop_the_participation_on_a_specific_date()
    {
        // Given: The configured date for the end of participation is set to '4th of december'
        Config::set('santa.end_participation.day', 4);
        Config::set('santa.end_participation.month', 12);

        // Given: The current date is set to the 4th of december
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 4));

        // Given: The state is set to STARTED
        State::set('bot', State::STARTED);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function() use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // When: The end participation job is called
        dispatch(new EndParticipation());

        // Then: The bot state should be set to DRAWING
        $this->assertEquals(State::DRAWING, State::byName('bot'));

        // And: The old announcement post should have been deleted
        $this->assertEquals('12345', $service->deletedPost);
        $this->assertEquals('67890', $service->deletedPostChannel);

        // Also: A new announcement post should have been written to the announcements channel
        $this->assertEquals(67890, $service->channelId);
        $this->assertNotEmpty($service->message);

        // And: The new announcement id should have been saved
        $this->assertEquals(1234, State::byName('announcement_id'));
    }

    // TODO: If date is not set, skip it
    // TODO: If state is not started, do nothing
}
