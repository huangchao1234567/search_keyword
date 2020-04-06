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
        $new->sogouGather('商标注册');
        $this->assertTrue(true);
    }
}
