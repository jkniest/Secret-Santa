<?php

namespace Tests\Unit;

use App\Discord\JsonMessage;
use App\Discord\MessageService;
use Tests\Fakes\FakeMessageService;
use Tests\TestCase;

class JsonMessageTest extends TestCase
{
    /**
     * @var FakeMessageService
     */
    private $service;

    /**
     * @var JsonMessage
     */
    private $message;

    protected function setUp()
    {
        parent::setUp();

        $this->service = new FakeMessageService();
        app()->singleton(MessageService::class, function () {
            return $this->service;
        });

        $json = [
            'id'         => '1234',
            'author'     => [
                'username' => 'Some user',
                'id'       => '5678'
            ],
            'content'    => 'I am a content',
            'channel_id' => '9101112'
        ];

        $this->message = new JsonMessage($json);
    }

    /** @test */
    public function it_can_be_deleted()
    {
        // When: We delete the message
        $this->message->delete();

        // Then: The message should have been deleted
        $this->assertEquals('1234', $this->service->deletedPost);
        $this->assertEquals('9101112', $this->service->deletedPostChannel);
    }

    /** @test */
    public function it_can_send_a_reply()
    {
        // When: We send a reply
        $this->message->reply('thank you!');

        // Then: The message should have been sent
        $this->assertEquals('<@5678>, thank you!', $this->service->message);
        $this->assertEquals('9101112', $this->service->channelId);
    }

    /** @test */
    public function it_can_send_a_direct_message_to_the_author()
    {
        // When: We send a DM to the user
        $this->message->sendDm('I love you!');

        // Then: The message should have been sent
        $this->assertEquals('I love you!', $this->service->message);
        $this->assertEquals('5678', $this->service->channelId);
    }

    /** @test */
    public function it_can_return_the_id_of_the_author()
    {
        $this->assertEquals('5678', $this->message->getAuthor()->getId());
    }

    /** @test */
    public function it_can_return_the_username_of_the_author()
    {
        $this->assertEquals('Some user', $this->message->getAuthor()->getUsername());
    }

    /** @test */
    public function it_can_return_the_content()
    {
        $this->assertEquals('I am a content', $this->message->getContent());
    }

    /** @test */
    public function it_can_return_the_channel_id()
    {
        $this->assertEquals('9101112', $this->message->getChannelId());
    }

    /** @test */
    public function it_can_return_the_id_of_the_message()
    {
        $this->assertEquals('1234', $this->message->getId());
    }

    /** @test */
    public function it_can_send_a_static_reply_to_the_user()
    {
        // When: We send a static reply
        $this->message->staticReply('This is static!');

        // Then: The reply should have been sent
        $this->assertEquals('This is static!', $this->service->message);
        $this->assertEquals('9101112', $this->service->channelId);
    }
}
