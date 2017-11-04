<?php

namespace Tests\Unit;

use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_the_default_state_started()
    {
        $state = State::whereName('bot')->first()->value;

        $this->assertEquals(State::STARTED, $state);
    }

    /** @test */
    public function it_can_return_the_value_of_a_state()
    {
        $this->create(State::class, [
            'name'  => 'example',
            'value' => 'value'
        ]);

        $this->assertEquals('value', State::byName('example'));
    }

    /** @test */
    public function it_can_set_the_value_of_a_state()
    {
        $this->create(State::class, [
            'name'  => 'example',
            'value' => 'value'
        ]);

        $this->assertDatabaseHas('states', [
            'name'  => 'example',
            'value' => 'value'
        ]);

        State::set('example', 'another');

        $this->assertDatabaseMissing('states', [
            'name'  => 'example',
            'value' => 'value'
        ]);

        $this->assertDatabaseHas('states', [
            'name'  => 'example',
            'value' => 'another'
        ]);
    }
}
