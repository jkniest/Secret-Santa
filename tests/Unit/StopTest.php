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
        State::set('bot', State::STOPPED);

        // Given: A fakes messaging service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement id and channel should be '12345' and '67890'
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

        // And: The state should have been set to STOPPED
        $this->assertEquals(State::STOPPED, State::byName('bot'));
    }

    // TODO: Validate date
    // TODO: Validate state
    // TODO: Refactoring
}
