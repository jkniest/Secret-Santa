<?php

namespace Tests\Unit;

use App\Discord\MessageService;
use App\Jobs\Stop;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMessageService;
use Tests\TestCase;

class StopTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_will_stop_the_bot_automatically()
    {
        // Given: The date is two days after new-year
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 1, 3, 0));

        // Given: The state is set to IDLE
        State::set('bot', State::IDLE);

        // Given: A fakes messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement id and channel id are '12345' and '67890'
        State::set('announcement_id', '12345');
        State::set('announcement_channel', '67890');

        // When: The stop command is running
        dispatch(new Stop());

        // Then: The state should be set to STOPPED
        $this->assertEquals(State::STOPPED, State::byName('bot'));

        // And: The old announcement message should be deleted
        $this->assertEquals('12345', $service->deletedPost);
        $this->assertEquals('67890', $service->deletedPostChannel);

        // Also: The announcement id should be set to null
        $this->assertNull(State::byName('announcement_id'));
    }

    /** @test */
    public function it_will_only_execute_two_days_after_new_year()
    {
        // Given: The date is four days after new-year
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 1, 5, 0));

        // Given: The state is set to IDLE
        State::set('bot', State::IDLE);

        // Given: A fakes messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement id and channel id are '12345' and '67890'
        State::set('announcement_id', '12345');
        State::set('announcement_channel', '67890');

        // When: The stop command is running
        dispatch(new Stop());

        // Then: The state should not have changed
        $this->assertEquals(State::IDLE, State::byName('bot'));

        // And: The old announcement message should not be deleted
        $this->assertEmpty($service->deletedPost);

        // Also: The announcement id should not be null
        $this->assertNotNull(State::byName('announcement_id'));
    }

    /** @test */
    public function it_will_only_execute_if_the_hour_is_zero()
    {
        // Given: The date is two days and six hours after new-year
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 1, 3, 6));

        // Given: The state is set to IDLE
        State::set('bot', State::IDLE);

        // Given: A fakes messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement id and channel id are '12345' and '67890'
        State::set('announcement_id', '12345');
        State::set('announcement_channel', '67890');

        // When: The stop command is running
        dispatch(new Stop());

        // Then: The state should not have changed
        $this->assertEquals(State::IDLE, State::byName('bot'));

        // And: The old announcement message should not be deleted
        $this->assertEmpty($service->deletedPost);

        // Also: The announcement id should not be null
        $this->assertNotNull(State::byName('announcement_id'));
    }

    /** @testf */
    public function it_will_only_execute_if_the_minute_is_zero()
    {
        // Given: The date is two days and fifteen minutes after new-year
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 1, 3, 0, 15));

        // Given: The state is set to IDLE
        State::set('bot', State::IDLE);

        // Given: A fakes messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement id and channel id are '12345' and '67890'
        State::set('announcement_id', '12345');
        State::set('announcement_channel', '67890');

        // When: The stop command is running
        dispatch(new Stop());

        // Then: The state should not have changed
        $this->assertEquals(State::IDLE, State::byName('bot'));

        // And: The old announcement message should not be deleted
        $this->assertEmpty($service->deletedPost);

        // Also: The announcement id should not be null
        $this->assertNotNull(State::byName('announcement_id'));
    }

    // TODO: Validate state
    // TODO: Refactoring
}
