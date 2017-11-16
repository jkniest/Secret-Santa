<?php

namespace Tests\Unit;

use App\Stub;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StubTest extends TestCase
{
    /** @test */
    public function it_can_load_a_specific_stub_file()
    {
        $content = Stub::load('test');

        $this->assertEquals('This is a {{placeholder}}', $content);
    }

    /** @test */
    public function it_can_replace_variables()
    {
        $content = Stub::load('test', [
            'placeholder' => 'unit test'
        ]);

        $this->assertEquals('This is a unit test', $content);
    }
}
