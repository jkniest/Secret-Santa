<?php

namespace Tests\Unit;

use Tests\Fakes\FakeMessage;
use Tests\TestCase;

class FakeMessageTest extends TestCase
{
    /**
     * @var FakeMessage
     */
    private $message;

    protected function setUp()
    {
        parent::setUp();

        $this->message = new FakeMessage('This is a sample content');
    }

    /** @test */
    public function it_can_be_deleted()
    {
        $this->message->delete();

        $this->assertTrue($this->message->isDeleted);
    }

    /** @test */
    public function it_can_send_a_reply()
    {
        $this->message->reply('This is a reply');

        $this->assertEquals('This is a reply', $this->message->replyText);
    }

    /** @test */
    public function it_can_send_a_direct_message_to_the_author()
    {
        $this->message->sendDM('This is a direct message');

        $this->assertEquals('This is a direct message', $this->message->dmText);
    }

    /** @test */
    public function it_can_return_the_id_of_the_author()
    {
        $this->assertEquals('123456789', $this->message->getAuthor()->getId());
    }

    /** @test */
    public function it_can_return_the_username_of_the_author()
    {
        $this->assertEquals('random123', $this->message->getAuthor()->getUsername());
    }

    /** @test */
    public function it_can_return_the_content()
    {
        $this->assertEquals('This is a sample content', $this->message->getContent());
    }
}
