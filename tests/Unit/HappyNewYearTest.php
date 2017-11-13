<?php

namespace Tests\Unit;

use App\Discord\MessageService;
use App\Jobs\HappyNewYear;
use App\Models\Participant;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMessageService;
use Tests\TestCase;

class HappyNewYearTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_will_send_a_message_on_new_year()
    {
        // Given: The current state is DRAWING
        State::set('bot', State::DRAWING);

        // Given: The current date is the first january of the next year
        Carbon::setTestNow(Carbon::create(Carbon::now()->year + 1, 1, 1, 0));

        // Given: The old announcement id is '12345' and the old announcement channel is '67890'
        State::set('announcement_id', '12345');
        State::set('announcement_channel', '67890');

        // Given: A faked message service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: There are 3 participants
        $this->create(Participant::class, [], 3);

        // When: The HappyNewYear command is executed
        dispatch(new HappyNewYear());

        // Then: The old announcement post should have been deleted
        $this->assertEquals('12345', $service->deletedPost);
        $this->assertEquals('67890', $service->deletedPostChannel);

        // And: A new announcement post should have been sent
        $this->assertEquals('67890', $service->channelId);
        $this->assertNotEmpty($service->message);

        // Also: The state should have changed to 'IDLE'
        $this->assertEquals(State::IDLE, State::byName('bot'));

        // And: The participant list should be empty
        $this->assertCount(0, Participant::all());
    }

    /** @test */
    public function it_will_not_run_if_its_not_new_year()
    {
        // Given: The current state is DRAWING
        State::set('bot', State::DRAWING);

        // Given: The current date is the second january of the next year
        Carbon::setTestNow(Carbon::create(Carbon::now()->year + 1, 1, 2));

        // Given: The old announcement id is '12345' and the old announcement channel is '67890'
        State::set('announcement_id', '12345');
        State::set('announcement_channel', '67890');

        // Given: A faked message service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: There are 3 participants
        $this->create(Participant::class, [], 3);

        // When: The HappyNewYear command is executed
        dispatch(new HappyNewYear());

        // Then: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);

        // And: No new announcement post should have been sent
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // Also: The state should not have changed
        $this->assertEquals(State::DRAWING, State::byName('bot'));

        // And: The participant list should not be empty
        $this->assertCount(3, Participant::all());
    }

    /** @test */
    public function it_will_only_execute_if_the_hour_is_zero()
    {
        // Given: The current state is DRAWING
        State::set('bot', State::DRAWING);

        // Given: The current date is the first january of the next year but on 1pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year + 1, 1, 1, 1));

        // Given: The old announcement id is '12345' and the old announcement channel is '67890'
        State::set('announcement_id', '12345');
        State::set('announcement_channel', '67890');

        // Given: A faked message service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: There are 3 participants
        $this->create(Participant::class, [], 3);

        // When: The HappyNewYear command is executed
        dispatch(new HappyNewYear());

        // Then: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);

        // And: No new announcement post should have been sent
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // Also: The state should not have changed
        $this->assertEquals(State::DRAWING, State::byName('bot'));

        // And: The participant list should not be empty
        $this->assertCount(3, Participant::all());
    }

    /** @test */
    public function it_will_only_execute_if_the_minute_is_zero()
    {
        // Given: The current state is DRAWING
        State::set('bot', State::DRAWING);

        // Given: The current date is the first january of the next year but on 0:15pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year + 1, 1, 1, 0, 15));

        // Given: The old announcement id is '12345' and the old announcement channel is '67890'
        State::set('announcement_id', '12345');
        State::set('announcement_channel', '67890');

        // Given: A faked message service
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: There are 3 participants
        $this->create(Participant::class, [], 3);

        // When: The HappyNewYear command is executed
        dispatch(new HappyNewYear());

        // Then: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);

        // And: No new announcement post should have been sent
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // Also: The state should not have changed
        $this->assertEquals(State::DRAWING, State::byName('bot'));

        // And: The participant list should not be empty
        $this->assertCount(3, Participant::all());
    }

    // TODO: Validate state
    // TODO: Refactoring
}
