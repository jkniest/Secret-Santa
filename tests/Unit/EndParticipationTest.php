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
        // Given: The configured date for the end of participation is set to '4th of december at 9am'
        Config::set('santa.end_participation.day', 4);
        Config::set('santa.end_participation.month', 12);
        Config::set('santa.end_participation.hour', 9);

        // Given: The current date is set to the 4th of december at 9am
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 4, 9));

        // Given: The state is set to STARTED
        State::set('bot', State::STARTED);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
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

    /** @test */
    public function if_the_date_is_not_set_dont_do_anything()
    {
        // Given: The state is set to STARTED
        State::set('bot', State::STARTED);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // When: The end participation job is called
        dispatch(new EndParticipation());

        // Then: The bot state should not have changed
        $this->assertEquals(State::STARTED, State::byName('bot'));

        // And: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);
        $this->assertEmpty($service->deletedPostChannel);

        // Also: No post should have been written to the announcements channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);
    }

    /** @test */
    public function if_the_day_is_not_correct_dont_do_anything()
    {
        // Given: The configured date for the end of participation is set to '4th of december at 9am'
        Config::set('santa.end_participation.day', 4);
        Config::set('santa.end_participation.month', 12);
        Config::set('santa.end_participation.hour', 9);

        // Given: The current date is set to the 10th of december at 9am
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 10, 9));

        // Given: The state is set to STARTED
        State::set('bot', State::STARTED);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // When: The end participation job is called
        dispatch(new EndParticipation());

        // Then: The bot state should not have changed
        $this->assertEquals(State::STARTED, State::byName('bot'));

        // And: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);
        $this->assertEmpty($service->deletedPostChannel);

        // Also: No post should have been written to the announcements channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);
    }

    /** @test */
    public function if_the_month_is_not_correct_dont_do_anything()
    {
        // Given: The configured date for the end of participation is set to '4th of december at 9am'
        Config::set('santa.end_participation.day', 4);
        Config::set('santa.end_participation.month', 12);
        Config::set('santa.end_participation.hour', 9);

        // Given: The current date is set to the 4th of may at 9am
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 5, 4, 9));

        // Given: The state is set to STARTED
        State::set('bot', State::STARTED);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // When: The end participation job is called
        dispatch(new EndParticipation());

        // Then: The bot state should not have changed
        $this->assertEquals(State::STARTED, State::byName('bot'));

        // And: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);
        $this->assertEmpty($service->deletedPostChannel);

        // Also: No post should have been written to the announcements channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);
    }

    /** @test */
    public function if_the_hour_is_not_correct_dont_do_anything()
    {
        // Given: The configured date for the end of participation is set to '4th of december at 9am'
        Config::set('santa.end_participation.day', 4);
        Config::set('santa.end_participation.month', 12);
        Config::set('santa.end_participation.hour', 9);

        // Given: The current date is set to the 4th of december at 1pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 4, 13));

        // Given: The state is set to STARTED
        State::set('bot', State::STARTED);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // When: The end participation job is called
        dispatch(new EndParticipation());

        // Then: The bot state should not have changed
        $this->assertEquals(State::STARTED, State::byName('bot'));

        // And: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);
        $this->assertEmpty($service->deletedPostChannel);

        // Also: No post should have been written to the announcements channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);
    }

    /** @test */
    public function if_the_minute_is_not_zero_dont_do_anything()
    {
        // Given: The configured date for the end of participation is set to '4th of december at 9am'
        Config::set('santa.end_participation.day', 4);
        Config::set('santa.end_participation.month', 12);
        Config::set('santa.end_participation.hour', 9);

        // Given: The current date is set to the 4th of december at 9:15am
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 4, 9, 15));

        // Given: The state is set to STARTED
        State::set('bot', State::STARTED);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // When: The end participation job is called
        dispatch(new EndParticipation());

        // Then: The bot state should not have changed
        $this->assertEquals(State::STARTED, State::byName('bot'));

        // And: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);
        $this->assertEmpty($service->deletedPostChannel);

        // Also: No post should have been written to the announcements channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);
    }

    /** @test */
    public function if_the_state_is_drawing_dont_do_anything()
    {
        // Given: The configured date for the end of participation is set to '4th of december at 9am'
        Config::set('santa.end_participation.day', 4);
        Config::set('santa.end_participation.month', 12);
        Config::set('santa.end_participation.hour', 9);

        // Given: The current date is set to the 4th of december at 9am
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 4, 9));

        // Given: The state is set to DRAWING
        State::set('bot', State::DRAWING);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // When: The end participation job is called
        dispatch(new EndParticipation());

        // Then: The bot state should not have changed
        $this->assertEquals(State::DRAWING, State::byName('bot'));

        // And: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);
        $this->assertEmpty($service->deletedPostChannel);

        // Also: No post should have been written to the announcements channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);
    }

    /** @test */
    public function if_the_state_is_idle_dont_do_anything()
    {
        // Given: The configured date for the end of participation is set to '4th of december at 9am'
        Config::set('santa.end_participation.day', 4);
        Config::set('santa.end_participation.month', 12);
        Config::set('santa.end_participation.hour', 9);

        // Given: The current date is set to the 4th of december at 9am
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 4, 9));

        // Given: The state is set to IDLE
        State::set('bot', State::IDLE);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // When: The end participation job is called
        dispatch(new EndParticipation());

        // Then: The bot state should not have changed
        $this->assertEquals(State::IDLE, State::byName('bot'));

        // And: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);
        $this->assertEmpty($service->deletedPostChannel);

        // Also: No post should have been written to the announcements channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);
    }

    /** @test */
    public function if_the_state_is_stopped_dont_do_anything()
    {
        // Given: The configured date for the end of participation is set to '4th of december at 9am'
        Config::set('santa.end_participation.day', 4);
        Config::set('santa.end_participation.month', 12);
        Config::set('santa.end_participation.hour', 9);

        // Given: The current date is set to the 4th of december at 9am
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 4, 9));

        // Given: The state is set to STOPPED
        State::set('bot', State::STOPPED);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // When: The end participation job is called
        dispatch(new EndParticipation());

        // Then: The bot state should not have changed
        $this->assertEquals(State::STOPPED, State::byName('bot'));

        // And: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);
        $this->assertEmpty($service->deletedPostChannel);

        // Also: No post should have been written to the announcements channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);
    }

    // TODO: Config file and .env.example file
}
