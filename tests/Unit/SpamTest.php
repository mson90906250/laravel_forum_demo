<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
    /** @test */
    public function it_can_detect_invalid_words()
    {
        $spam = new Spam;

        $this->assertFalse($spam->detect('Innocent Word'));

        $this->expectException(\Exception::class);

        $spam->detect('Dirty Word');
    }

    /** @test */
    public function it_can_detect_key_held_down()
    {
        $spam = new Spam;

        $this->expectException('Exception');

        $spam->detect('hello world aaaaaa');
    }
}
