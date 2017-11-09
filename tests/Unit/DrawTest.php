<?php

namespace Tests\Unit;

use App\Discord\MessageService;
use App\Jobs\Draw;
use App\Models\Participant;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\Fakes\FakeMessageService;
use Tests\TestCase;

class DrawTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_draw_all_participants_at_a_given_date()
    {
        // Given: The date for drawing is the 10th of december, at 4pm
        Config::set('santa.draw.month', 12);
        Config::set('santa.draw.day', 10);
        Config::set('santa.draw.hour', 16);

        // Given: The current date is set to the 10th of december at 4pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 10, 16));

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

        // When: The draw job is called
        dispatch(new Draw());

        // Then: Every participant should have a partner (and no doubles)
        $partners = Participant::all()->map->partner_id;
        $this->assertEquals(10, $partners->count());
        $this->assertEquals(10, $partners->unique()->count());

        // And: No participant should have themself
        Participant::all()->each(function ($participant) {
            $this->assertNotEquals($participant->discord_user_id, $participant->partner_id);
        });

        // Also: Every participant should have get an e-mail with detailed information
        $this->assertCount(10, $service->dmMessages);

        $participants = Participant::all()->toArray();
        for ($i = 0; $i < count($participants); $i++) {
            $participant = Participant::all()[$i];
            $this->assertContains("<@{$participant['partner_id']}>", $service->dmMessages[$i]);
        }

        // And: The old announcement post should have been deleted
        $this->assertEquals('12345', $service->deletedPost);
        $this->assertEquals('67890', $service->deletedPostChannel);

        // Also: A new announcement post should have been written to the announcements channel
        $this->assertEquals(67890, $service->channelId);
        $this->assertNotEmpty($service->message);

        // And: The new announcement id should have been saved
        $this->assertEquals(1234, State::byName('announcement_id'));
    }

    /** @test */
    public function if_the_date_is_not_set_dont_do_anything()
    {
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

        // When: The draw job is called
        dispatch(new Draw());

        // Then: No participant should have a partner
        $this->assertTrue(Participant::all()->every(function ($participant) {
            return is_null($participant->partner_id);
        }));

        // Also: No direct messages should have been sent
        $this->assertCount(0, $service->dmMessages);

        // And: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);

        // Also: No new announcement post should have been written
        $this->assertEmpty($service->message);
    }

    /** @test */
    public function if_the_month_is_wrong_dont_do_anything()
    {
        // Given: The date for drawing is the 10th of december, at 4pm
        Config::set('santa.draw.month', 12);
        Config::set('santa.draw.day', 10);
        Config::set('santa.draw.hour', 16);

        // Given: The current date is set to the 10th of november at 4pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 11, 10, 16));

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

        // When: The draw job is called
        dispatch(new Draw());

        // Then: No participant should have a partner
        $this->assertTrue(Participant::all()->every(function ($participant) {
            return is_null($participant->partner_id);
        }));

        // Also: No direct messages should have been sent
        $this->assertCount(0, $service->dmMessages);

        // And: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);

        // Also: No new announcement post should have been written
        $this->assertEmpty($service->message);
    }

    /** @test */
    public function if_the_day_is_wrong_dont_do_anything()
    {
        // Given: The date for drawing is the 10th of december, at 4pm
        Config::set('santa.draw.month', 12);
        Config::set('santa.draw.day', 10);
        Config::set('santa.draw.hour', 16);

        // Given: The current date is set to the 14th of december at 4pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 14, 16));

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

        // When: The draw job is called
        dispatch(new Draw());

        // Then: No participant should have a partner
        $this->assertTrue(Participant::all()->every(function ($participant) {
            return is_null($participant->partner_id);
        }));

        // Also: No direct messages should have been sent
        $this->assertCount(0, $service->dmMessages);

        // And: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);

        // Also: No new announcement post should have been written
        $this->assertEmpty($service->message);
    }

    /** @test */
    public function if_the_hour_is_wrong_dont_do_anything()
    {
        // Given: The date for drawing is the 10th of december, at 4pm
        Config::set('santa.draw.month', 12);
        Config::set('santa.draw.day', 10);
        Config::set('santa.draw.hour', 16);

        // Given: The current date is set to the 10th of december at 8am
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 10, 8));

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

        // When: The draw job is called
        dispatch(new Draw());

        // Then: No participant should have a partner
        $this->assertTrue(Participant::all()->every(function ($participant) {
            return is_null($participant->partner_id);
        }));

        // Also: No direct messages should have been sent
        $this->assertCount(0, $service->dmMessages);

        // And: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);

        // Also: No new announcement post should have been written
        $this->assertEmpty($service->message);
    }

    /** @test */
    public function if_the_minute_is_not_zero_dont_do_anything()
    {
        // Given: The date for drawing is the 10th of december, at 4pm
        Config::set('santa.draw.month', 12);
        Config::set('santa.draw.day', 10);
        Config::set('santa.draw.hour', 16);

        // Given: The current date is set to the 10th of december at 4:15pm
        Carbon::setTestNow(Carbon::create(Carbon::now()->year, 12, 10, 16, 15));

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

        // When: The draw job is called
        dispatch(new Draw());

        // Then: No participant should have a partner
        $this->assertTrue(Participant::all()->every(function ($participant) {
            return is_null($participant->partner_id);
        }));

        // Also: No direct messages should have been sent
        $this->assertCount(0, $service->dmMessages);

        // And: The old announcement post should not have been deleted
        $this->assertEmpty($service->deletedPost);

        // Also: No new announcement post should have been written
        $this->assertEmpty($service->message);
    }

    // TODO: When State != DRAWING, don't do anything
    // TODO: When 0 participants, don't do anything
    // TODO: Add config values and .env file
    // TODO: Replace static dates with dynamic in existing texts
    // TODO: Fix jsonMessage DM sending (see DiscordMessageService)
    // TODO: Date in first DM should also be dynamic
}
