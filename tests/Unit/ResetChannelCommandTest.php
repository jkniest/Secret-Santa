<?php

namespace Tests\Unit;

use App\Models\State;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    // TODO: Validate state == STOPPED
    // TODO: Refactoring
}
