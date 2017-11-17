<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResetChannelCommand extends TestCase
{
    /** @test */
    public function it_can_reset_the_channel()
    {
        $this->assertEquals('a', 'a');
    }

    // TODO: Best-case test
    // TODO: Validate state == STOPPED
    // TODO: Refactoring
}
