<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */

    public function test_user_exists()
    {
        $user = User::where('email', 'faisal@base29.com')->first();
        if ($user) {
            $this->assertTrue('YaY');
        }

    }
}