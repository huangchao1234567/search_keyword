<?php

namespace Tests\Feature;

use App\Server\Keyword;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $new=new Keyword();

        $new->keywordRun();
        $this->assertTrue(true);
    }

    public function testFanyi()
    {
        dd(12);
    }
}
