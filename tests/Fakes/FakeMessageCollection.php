<?php

namespace Tests\Fakes;

class FakeMessageCollection
{
    public function delete(FakeMessage $message)
    {
        $message->isDeleted = true;
    }
}