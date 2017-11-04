<?php

namespace Tests\Fakes;

use App\Discord\User;

class FakeUser implements User
{
    public function getId()
    {
        return '123456789';
    }

    public function getUsername()
    {
        return 'random123';
    }
}