<?php

namespace Tests\Unit;

use App\Discord\Commands\MarkCommand;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMessage;
use Tests\TestCase;

class MarkCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_mark_the_announcement_channel()
    {
        // Given: The announcement channel is set to null
        State::set('announcement_channel', null);

        // Given: The state is set to stopped
        State::set('bot', State::STOPPED);

        // When: The command is executed with a fake message
        $message = new FakeMessage;
        (new MarkCommand($message))->handle();

        // Then: The channel id of the message should have been set as the announcement channel
        $this->assertEquals('6789', State::byName('announcement_channel'));

        // And: The command message should have been deleted
        $this->assertTrue($message->isDeleted);
    }

    /** @test */
    public function it_does_nothing_if_there_is_already_an_announcement_channel()
    {
        // Given: The announcement channel is set to '1234'
        State::set('announcement_channel', '1234');

        // Given: The state is set to stopped
        State::set('bot', State::STOPPED);

        // When: The command is executed with a fake message
        $message = new FakeMessage;
        (new MarkCommand($message))->handle();

        // Then: The channel id of the message should not have been changed
        $this->assertEquals('1234', State::byName('announcement_channel'));

        // And: The command message should not have been deleted
        $this->assertFalse($message->isDeleted);
    }
}
