<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StartTest extends TestCase
{
    /** @test */
    public function it_can_start_the_bot_at_a_given_date()
    {
        $this->assertEquals('a', 'a');
    }

    // TODO: Best case test
    // TODO: Validate date
    // TODO: Validate state (== stopped)
    // TODO: Validate announcement_channel state (not null)
    // TODO: Remove "santa start" command
    // TODO: Implement "santa mark" command
}
