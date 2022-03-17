<?php

namespace Modules\PublicAPI\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Modules\PublicAPI\base64_rand;

class HelpersTest extends TestCase
{
    public function test_base64_rand()
    {
        $str = base64_rand(32);
        $this->assertTrue(strlen($str) === 32);
        $this->assertNotFalse(base64_decode($str));

        $str = base64_rand(16);
        $this->assertTrue(strlen($str) === 16);

        $str1 = base64_rand(16, 'some_seed');
        $str2 = base64_rand(16, 'some_seed');
        $str3 = base64_rand(16, 'some_another_seed');

        $this->assertSame($str1, $str2);
        $this->assertNotSame($str2, $str3);
    }
}
