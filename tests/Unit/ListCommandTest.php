<?php

namespace Tests\Unit;

use App\Discord\Commands\ListCommand;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMessage;
use Tests\TestCase;

class ListCommandTest extends TestCase
{
    use RefreshDatabase;

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
}
