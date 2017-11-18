<?php

namespace Tests\Unit;

use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResetChannelCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_reset_the_channel()
    {
        // Given: The announcement channel is set to 12345
        State::set('announcement_channel', '12345');

        // Given: The state is set to STOPPED
        State::set('bot', State::STOPPED);

        // When: We execute the artisan command
        $this->artisan('reset:channel');

        // Then: The announcement channel should be null
        $this->assertNull(State::byName('announcement_channel'));
    }

    /** @test */
    public function it_does_nothing_if_the_state_is_started()
    {
        // Given: The announcement channel is set to 12345
        State::set('announcement_channel', '12345');

        // Given: The state is set to STARTED
        State::set('bot', State::STARTED);

        // When: We execute the artisan command
        $this->artisan('reset:channel');

        // Then: The announcement channel should not have changed
        $this->assertEquals('12345', State::byName('announcement_channel'));
    }

    /** @test */
    public function it_does_nothing_if_the_state_is_drawing()
    {
        // Given: The announcement channel is set to 12345
        State::set('announcement_channel', '12345');

        // Given: The state is set to DRAWING
        State::set('bot', State::DRAWING);

        // When: We execute the artisan command
        $this->artisan('reset:channel');

        // Then: The announcement channel should not have changed
        $this->assertEquals('12345', State::byName('announcement_channel'));
    }

    /** @test */
    public function it_does_nothing_if_the_state_is_idle()
    {
        // Given: The announcement channel is set to 12345
        State::set('announcement_channel', '12345');

        // Given: The state is set to IDLE
        State::set('bot', State::IDLE);

        // When: We execute the artisan command
        $this->artisan('reset:channel');

        // Then: The announcement channel should not have changed
        $this->assertEquals('12345', State::byName('announcement_channel'));
    }
}
