<?php

namespace Tests\Unit;

use App\Discord\Commands\ListCommand;
use App\Models\Participant;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMessage;
use Tests\TestCase;

class ListCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();

        State::set('bot', State::STARTED);
    }

    /** @test */
    public function it_can_list_all_participants()
    {
        // Given: There are three participants with the id's 12345, 67890, 10111213
        $this->create(Participant::class, ['discord_user_id' => '12345']);
        $this->create(Participant::class, ['discord_user_id' => '67890']);
        $this->create(Participant::class, ['discord_user_id' => '10111213']);

        // Given: A faked message
        $message = new FakeMessage();

        // Given: The list command with the faked message
        $command = new ListCommand($message);

        // When: This method is executed
        $command->handle();

        // Then: The message should be deleted
        $this->assertTrue($message->isDeleted);

        // And: A reply should have been send with all participants
        $this->assertContains('<@12345>', $message->replyText);
        $this->assertContains('<@67890>', $message->replyText);
        $this->assertContains('<@10111213>', $message->replyText);
    }

    /** @test */
    public function it_sends_a_message_if_no_participants_exists()
    {
        // Given: A fake message
        $message = new FakeMessage();

        // Given: A list command with the faked message
        $command = new ListCommand($message);

        // When: This command is executed
        $command->handle();

        // Then: The message should be deleted
        $this->assertTrue($message->isDeleted);

        // And: A generic reply should be send
        $this->assertEquals('Leider nehmen noch keine Personen an dem Spiel teil.', $message->replyText);
    }

    /** @test */
    public function the_command_is_not_executed_if_the_state_is_stopped()
    {
        // Given: The state is STOPPED
        State::set('bot', State::STOPPED);

        // Given: The command with a fake message
        $message = new FakeMessage();
        $command = new ListCommand($message);

        // When: We run the command
        $command->handle();

        // Then: No reply should have been send
        $this->assertEmpty($message->replyText);
    }

    /** @test */
    public function the_command_is_not_executed_if_the_state_is_drawing()
    {
        // Given: The state is DRAWING
        State::set('bot', State::DRAWING);

        // Given: The command with a fake message
        $message = new FakeMessage();
        $command = new ListCommand($message);

        // When: We run the command
        $command->handle();

        // Then: The reply should have been send
        $this->assertNotEmpty($message->replyText);
    }

    /** @test */
    public function the_command_is_not_executed_if_the_state_is_idle()
    {
        // Given: The state is IDLE
        State::set('bot', State::IDLE);

        // Given: The command with a fake message
        $message = new FakeMessage();
        $command = new ListCommand($message);

        // When: We run the command
        $command->handle();

        // Then: The reply should not have been send
        $this->assertEmpty($message->replyText);
    }

}
