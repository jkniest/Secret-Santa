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

    /** @test */
    public function it_does_nothing_if_the_date_is_not_configured()
    {
        // Given: The configured start date is null
        Config::set('santa.start.hour', null);
        Config::set('santa.start.day', null);
        Config::set('santa.start.month', null);

        // Given: The announcement channel is set to '12345'
        State::set('announcement_channel', 12345);

        // Given: A faked messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // When: We execute the command
        dispatch(new Start());

        // Then: The bot state should not have changed to "STARTED"
        $this->assertEquals(State::STOPPED, State::byName('bot'));

        // And: Nomessage should have been send to the announcement channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // And: No announcement post id should have been saved
        $this->assertNull(State::byName('announcement_id'));
    }

    /** @test */
    public function it_does_nothing_if_the_current_hour_is_wrong()
    {
        // Given: The configured start date is the 3rd december at 4pm
        Config::set('santa.start.hour', 16);
        Config::set('santa.start.day', 3);
        Config::set('santa.start.month', 12);

        // Given: The current date and time is the 3rd december at 5pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 3, 17));

        // Given: The announcement channel is set to '12345'
        State::set('announcement_channel', 12345);

        // Given: A faked messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // When: We execute the command
        dispatch(new Start());

        // Then: The bot state should not have changed to "STARTED"
        $this->assertEquals(State::STOPPED, State::byName('bot'));

        // And: Nomessage should have been send to the announcement channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // And: No announcement post id should have been saved
        $this->assertNull(State::byName('announcement_id'));
    }

    /** @test */
    public function it_does_nothing_if_the_current_day_is_wrong()
    {
        // Given: The configured start date is the 3rd december at 4pm
        Config::set('santa.start.hour', 16);
        Config::set('santa.start.day', 3);
        Config::set('santa.start.month', 12);

        // Given: The current date and time is the 4th december at 4pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 4, 16));

        // Given: The announcement channel is set to '12345'
        State::set('announcement_channel', 12345);

        // Given: A faked messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // When: We execute the command
        dispatch(new Start());

        // Then: The bot state should not have changed to "STARTED"
        $this->assertEquals(State::STOPPED, State::byName('bot'));

        // And: Nomessage should have been send to the announcement channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // And: No announcement post id should have been saved
        $this->assertNull(State::byName('announcement_id'));
    }

    /** @test */
    public function it_does_nothing_if_the_current_month_is_wrong()
    {
        // Given: The configured start date is the 3rd december at 4pm
        Config::set('santa.start.hour', 16);
        Config::set('santa.start.day', 3);
        Config::set('santa.start.month', 12);

        // Given: The current date and time is the 3rd november at 4pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 11, 3, 16));

        // Given: The announcement channel is set to '12345'
        State::set('announcement_channel', 12345);

        // Given: A faked messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // When: We execute the command
        dispatch(new Start());

        // Then: The bot state should not have changed to "STARTED"
        $this->assertEquals(State::STOPPED, State::byName('bot'));

        // And: Nomessage should have been send to the announcement channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // And: No announcement post id should have been saved
        $this->assertNull(State::byName('announcement_id'));
    }

    /** @test */
    public function it_does_nothing_if_the_current_minute_is_not_zero()
    {
        // Given: The configured start date is the 3rd december at 4pm
        Config::set('santa.start.hour', 16);
        Config::set('santa.start.day', 3);
        Config::set('santa.start.month', 12);

        // Given: The current date and time is the 3rd december at 4:15pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 3, 16, 15));

        // Given: The announcement channel is set to '12345'
        State::set('announcement_channel', 12345);

        // Given: A faked messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // When: We execute the command
        dispatch(new Start());

        // Then: The bot state should not have changed to "STARTED"
        $this->assertEquals(State::STOPPED, State::byName('bot'));

        // And: Nomessage should have been send to the announcement channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // And: No announcement post id should have been saved
        $this->assertNull(State::byName('announcement_id'));
    }

    /** @test */
    public function it_does_nothing_if_the_state_is_started()
    {
        // Given: The configured start date is the 3rd december at 4pm
        Config::set('santa.start.hour', 16);
        Config::set('santa.start.day', 3);
        Config::set('santa.start.month', 12);

        // Given: The current date and time is the 3rd december at 4pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 3, 16));

        // Given: The announcement channel is set to '12345'
        State::set('announcement_channel', 12345);

        // Given: The state is set to started
        State::set('bot', State::STARTED);

        // Given: A faked messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // When: We execute the command
        dispatch(new Start());

        // And: Nomessage should have been send to the announcement channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // And: No announcement post id should have been saved
        $this->assertNull(State::byName('announcement_id'));
    }

    /** @test */
    public function it_does_nothing_if_the_state_is_drawing()
    {
        // Given: The configured start date is the 3rd december at 4pm
        Config::set('santa.start.hour', 16);
        Config::set('santa.start.day', 3);
        Config::set('santa.start.month', 12);

        // Given: The current date and time is the 3rd december at 4pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 3, 16));

        // Given: The announcement channel is set to '12345'
        State::set('announcement_channel', 12345);

        // Given: The state is set to drawing
        State::set('bot', State::DRAWING);

        // Given: A faked messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // When: We execute the command
        dispatch(new Start());

        // And: Nomessage should have been send to the announcement channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // And: No announcement post id should have been saved
        $this->assertNull(State::byName('announcement_id'));
    }

    /** @test */
    public function it_does_nothing_if_the_state_is_idle()
    {
        // Given: The configured start date is the 3rd december at 4pm
        Config::set('santa.start.hour', 16);
        Config::set('santa.start.day', 3);
        Config::set('santa.start.month', 12);

        // Given: The current date and time is the 3rd december at 4pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 3, 16));

        // Given: The announcement channel is set to '12345'
        State::set('announcement_channel', 12345);

        // Given: The state is set to idle
        State::set('bot', State::IDLE);

        // Given: A faked messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // When: We execute the command
        dispatch(new Start());

        // And: Nomessage should have been send to the announcement channel
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // And: No announcement post id should have been saved
        $this->assertNull(State::byName('announcement_id'));
    }


    // TODO: Validate announcement_channel state (not null)
    // TODO: Remove "santa start" command
    // TODO: Refactoring
    // TODO: Implement "santa mark" command
}
