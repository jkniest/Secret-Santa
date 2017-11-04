<?php

namespace Tests\Fakes;

class FakeChannel
{
    /**
     * @var FakeMessageCollection
     */
    public $messages;

    public function __construct()
    {
        $this->messages = new FakeMessageCollection();
    }
}