<?php

namespace Tests\Unit;

use App\Discord\Commands\StartCommand;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMessage;
use Tests\TestCase;

class StartCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_start_the_bot()
    {
        // Given: The command with a fake message
        $message = new FakeMessage();
        $command = new StartCommand($message);

        // When: We execute the command
        $command->handle();

        // Then: The bot state should have changed to "STARTED"
        $this->assertEquals(State::STARTED, State::byName('bot'));

        // And: The command message should have been deleted
        $this->assertTrue($message->isDeleted);

        // Also: A static reply should have been send to the channel
        $this->assertNotEmpty($message->staticReplyText);

        // And: The announcement post id should have been saved
        $this->assertEquals('1234', State::byName('announcement_id'));
    }

    // TODO: Only executed if state is stopped
}
