<?php

namespace Tests\Unit\Support;

use App\Support\Phone\PhoneNormalizer;
use PHPUnit\Framework\TestCase;

class PhoneNormalizerTest extends TestCase
{
    public function test_normalizes_08_prefix(): void
    {
        $this->assertEquals('628123456789', PhoneNormalizer::normalize('08123456789'));
    }

    public function test_normalizes_plus62_prefix(): void
    {
        $this->assertEquals('628123456789', PhoneNormalizer::normalize('+628123456789'));
    }

    public function test_normalizes_62_prefix(): void
    {
        $this->assertEquals('628123456789', PhoneNormalizer::normalize('628123456789'));
    }

    public function test_removes_spaces_and_dashes(): void
    {
        $this->assertEquals('628123456789', PhoneNormalizer::normalize('+62 812-345-6789'));
    }

    public function test_null_returns_null(): void
    {
        $this->assertNull(PhoneNormalizer::normalize(null));
    }

    public function test_empty_string_returns_null(): void
    {
        $this->assertNull(PhoneNormalizer::normalize(''));
    }

    public function test_whitespace_only_returns_null(): void
    {
        $this->assertNull(PhoneNormalizer::normalize('   '));
    }
}
