<?php

namespace Tests\Unit;

use App\Discord\Commands\DefaultCommand;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeMessage;
use Tests\TestCase;

class DefaultCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_add_a_new_user_into_the_game()
    {
        // Given: A command with a fake message
        $message = new FakeMessage();
        $command = new DefaultCommand($message);

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
        $this->assertEquals(implode("\n", [
            'Hey random123,',
            'wir freuen uns dich in unserem Wichtelspiel begrüßen zu dürfen. Die Auslosung wird ' .
            'komplett automatisch und zufällig durchgeführt. Folgende Daten sind für dich noch ' .
            'relevant:',
            '',
            '- Am 24. Dezember 2017 findet die Auslosung statt und du bekommst per DM die Info, ' .
            'wen du beschenken sollst.',
            '',
            '- Am 31. Dezember 2017 werden die Spiele dann verschenkt. Wir erinnern dich nochmal ' .
            'rechtzeitig dran.',
            '',
            'Alles gute,',
            'Secret Santa'
        ]), $message->dmText);
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
}

