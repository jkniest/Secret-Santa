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
    public function a_user_can_participate_in_the_game()
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
}

