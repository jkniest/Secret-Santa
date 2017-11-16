<?php

namespace Tests\Unit;

use App\Discord\Commands\CommandHandler;
use Tests\Fakes\Example2Command;
use Tests\Fakes\ExampleCommand;
use Tests\Fakes\FakeMessage;
use Tests\TestCase;

class CommandHandlerTest extends TestCase
{
    /** @test */
    public function it_can_detect_and_start_a_command_based_on_a_message()
    {
        $handler = new CommandHandler('!test', [
            'example' => ExampleCommand::class,
            'another' => Example2Command::class
        ]);

        $result = $handler->handle(new FakeMessage('!test example'));

        $this->assertEquals('example', $result);

        $result = $handler->handle(new FakeMessage('!test another'));

        $this->assertEquals('example 2', $result);
    }

    /** @test */
    public function it_can_have_a_default_command()
    {
        $handler = new CommandHandler('!test', [
            '' => ExampleCommand::class
        ]);

        $result = $handler->handle(new FakeMessage('!test'));

        $this->assertEquals('example', $result);
    }

    /** @test */
    public function it_does_nothing_if_no_command_was_found()
    {
        $handler = new CommandHandler('!test', [
            ''        => ExampleCommand::class,
            'example' => Example2Command::class
        ]);

        $result = $handler->handle(new FakeMessage('!test another'));

        $this->assertNull($result);
    }
}
