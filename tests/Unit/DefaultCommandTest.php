<?php

namespace Tests\Unit;

use App\Discord\Commands\DefaultCommand;
use App\Models\Participant;
use App\Models\State;
use Carbon\Carbon;
use Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMessage;
use Tests\TestCase;

class DefaultCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();

        State::set('bot', State::STARTED);
    }

    /** @test */
    public function it_can_add_a_new_user_into_the_game()
    {
        // Given: A command with a fake message
        $message = new FakeMessage();
        $command = new DefaultCommand($message);

        // Given: The draw date is at the 5th of december at 3pm
        Config::set('santa.draw.hour', 15);
        Config::set('santa.draw.day', 5);
        Config::set('santa.draw.month', 12);

        // When: This command is executed
        $command->handle();

        // Then: There should be a participant with the user id '123456789'
        $this->assertCount(1, Participant::all());

        $this->assertDatabaseHas('participants', [
            'discord_user_id' => '123456789'
        ]);

        // And: The message should have been deleted
        $this->assertTrue($message->isDeleted);

        // Also: The bot should have replied to the message
        $this->assertEquals('du bist nun für das Wichtelspiel eingetragen.', $message->replyText);

        // And: The user should have get a DM from the bot
        $year = Carbon::now()->year;
        $this->assertContains(" 5. Dezember {$year} um 15 Uhr", $message->dmText);
    }

    /** @test */
    public function it_can_remove_a_user_from_the_game_if_already_existant()
    {
        // Given: There is a participant, with the id '123456789'
        $this->create(Participant::class, ['discord_user_id' => '123456789']);

        // Given: A fake message
        $message = new FakeMessage();

        // Given: The default command with the fake message
        $command = new DefaultCommand($message);

        // When: We execute the command
        $command->handle();

        // Then: The user should have been removed from the participant list
        $this->assertCount(0, Participant::all());

        // And: The source message should have been deleted
        $this->assertTrue($message->isDeleted);

        // Also: The bot should reply
        $this->assertEquals('du bist nun für das Wichtelspiel ausgetragen. Schade :(', $message->replyText);
    }

    /** @test */
    public function the_command_is_only_executed_if_the_state_is_started()
    {
        // Given: The state is set to 'STOPPED'
        State::set('bot', State::STOPPED);

        // Given: The command with a fake message
        $message = new FakeMessage();
        $command = new DefaultCommand($message);

        // When: We execute the command
        $command->handle();

        // Then: The participants table should still be empty
        $this->assertCount(0, Participant::all());

        // And: No reply or DM should have been send
        $this->assertEmpty($message->replyText);
        $this->assertEmpty($message->dmText);
    }
}

