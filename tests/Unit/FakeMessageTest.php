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

        $this->message = new FakeMessage();
    }

    /** @test */
    public function a_fake_message_can_be_deleted()
    {
        $this->message->delete();

        $this->assertTrue($this->message->isDeleted);
    }

    /** @test */
    public function a_reply_can_be_saved_to_a_fake_message()
    {
        $this->message->reply('This is a reply');

        $this->assertEquals('This is a reply', $this->message->replyText);
    }

    /** @test */
    public function a_dm_can_be_send_to_a_user()
    {
        $this->message->sendDM('This is a direct message');

        $this->assertEquals('This is a direct message', $this->message->dmText);
    }

    /** @test */
    public function the_author_id_can_be_returned()
    {
        $this->assertEquals('123456789', $this->message->getAuthor()->getId());
    }

    /** @test */
    public function the_author_username_can_be_returned()
    {
        $this->assertEquals('random123', $this->message->getAuthor()->getUsername());
    }
}
