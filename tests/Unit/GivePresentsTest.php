<?php

namespace Tests\Unit;

use App\Discord\MessageService;
use App\Jobs\GivePresents;
use App\Models\Participant;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\Fakes\FakeMessageService;
use Tests\TestCase;

class GivePresentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_send_every_user_a_dm_that_they_should_send_their_presents()
    {
        // Given: The give date is set to the 23th of december on 3pm
        Config::set('santa.give.month', 12);
        Config::set('santa.give.day', 23);
        Config::set('santa.give.hour', 15);

        // Given: The current date and time is set to the 23th of december, 3pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 23, 15));

        // Given: The state is set to DRAWING
        State::set('bot', State::DRAWING);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // Given: There are 10 participants
        $this->create(Participant::class, [], 10);

        // When: The job is called
        dispatch(new GivePresents());

        // Then: The old announcement post should have been deleted
        $this->assertEquals('12345', $service->deletedPost);
        $this->assertEquals('67890', $service->deletedPostChannel);

        // And: A new announcement post should have been posted
        $this->assertEquals('67890', $service->channelId);
        $this->assertNotEmpty($service->message);

        // Also: Every participant should have get a dm with a generic info
        $this->assertCount(10, $service->dmMessages);
    }

    /** @test */
    public function if_the_date_is_not_set_dont_do_anything()
    {
        // Given: The give date is set to null
        Config::set('santa.give.month', null);
        Config::set('santa.give.day', null);
        Config::set('santa.give.hour', null);

        // Given: The state is set to DRAWING
        State::set('bot', State::DRAWING);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // Given: There are 10 participants
        $this->create(Participant::class, [], 10);

        // When: The job is called
        dispatch(new GivePresents());

        // Then: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);

        // And: No new announcement post should have been posted
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // Also: No participant should have get a dm with a generic info
        $this->assertCount(0, $service->dmMessages);
    }

    /** @test */
    public function if_the_month_is_wrong_dont_do_anything()
    {
        // Given: The give date is set to the 10th of december, at 3pm
        Config::set('santa.give.month', 12);
        Config::set('santa.give.day', 10);
        Config::set('santa.give.hour', 15);

        // Given: The current date is set to the 10th of november, at 3pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 11, 10, 15));

        // Given: The state is set to DRAWING
        State::set('bot', State::DRAWING);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // Given: There are 10 participants
        $this->create(Participant::class, [], 10);

        // When: The job is called
        dispatch(new GivePresents());

        // Then: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);

        // And: No new announcement post should have been posted
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // Also: No participant should have get a dm with a generic info
        $this->assertCount(0, $service->dmMessages);
    }

    /** @testf */
    public function if_the_day_is_wrong_dont_do_anything()
    {
        // Given: The give date is set to the 10th of december, at 3pm
        Config::set('santa.give.month', 12);
        Config::set('santa.give.day', 10);
        Config::set('santa.give.hour', 15);

        // Given: The current date is set to the 11th of december, at 3pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 11, 15));

        // Given: The state is set to DRAWING
        State::set('bot', State::DRAWING);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // Given: There are 10 participants
        $this->create(Participant::class, [], 10);

        // When: The job is called
        dispatch(new GivePresents());

        // Then: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);

        // And: No new announcement post should have been posted
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // Also: No participant should have get a dm with a generic info
        $this->assertCount(0, $service->dmMessages);
    }

    /** @testf */
    public function if_the_hour_is_wrong_dont_do_anything()
    {
        // Given: The give date is set to the 10th of december, at 3pm
        Config::set('santa.give.month', 12);
        Config::set('santa.give.day', 10);
        Config::set('santa.give.hour', 15);

        // Given: The current date is set to the 10th of december, at 9pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 10, 21));

        // Given: The state is set to DRAWING
        State::set('bot', State::DRAWING);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // Given: There are 10 participants
        $this->create(Participant::class, [], 10);

        // When: The job is called
        dispatch(new GivePresents());

        // Then: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);

        // And: No new announcement post should have been posted
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // Also: No participant should have get a dm with a generic info
        $this->assertCount(0, $service->dmMessages);
    }

    /** @testf */
    public function if_the_minute_is_not_zero_dont_do_anything()
    {
        // Given: The give date is set to the 10th of december, at 3pm
        Config::set('santa.give.month', 12);
        Config::set('santa.give.day', 10);
        Config::set('santa.give.hour', 15);

        // Given: The current date is set to the 10th of december, at 3:15pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 10, 15, 15));

        // Given: The state is set to DRAWING
        State::set('bot', State::DRAWING);

        // Given: The message service is faked
        $service = new FakeMessageService();
        app()->singleton(MessageService::class, function () use ($service) {
            return $service;
        });

        // Given: The announcement post id is 12345 and the channel id is 67890
        State::set('announcement_id', 12345);
        State::set('announcement_channel', 67890);

        // Given: There are 10 participants
        $this->create(Participant::class, [], 10);

        // When: The job is called
        dispatch(new GivePresents());

        // Then: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);

        // And: No new announcement post should have been posted
        $this->assertEmpty($service->channelId);
        $this->assertEmpty($service->message);

        // Also: No participant should have get a dm with a generic info
        $this->assertCount(0, $service->dmMessages);
    }


    // TODO: Validate state
    // TODO: Add dates to config file and .env.example
    // TODO: Changed static dates in older texts
    // TODO: Refactoring
}
