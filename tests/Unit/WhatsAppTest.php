<?php

namespace Tests\Unit;

use App\Support\Phone\PhoneNormalizer;
use App\Support\WhatsApp;
use PHPUnit\Framework\TestCase;

class WhatsAppTest extends TestCase
{
    /** @test */
    public function normalizes_08_prefix_to_62(): void
    {
        $this->assertSame('6281256971234', PhoneNormalizer::normalize('081256971234'));
    }

    /** @test */
    public function normalizes_plus_62_prefix(): void
    {
        $this->assertSame('6281256971234', PhoneNormalizer::normalize('+6281256971234'));
    }

    /** @test */
    public function keeps_62_prefix_unchanged(): void
    {
        $this->assertSame('6281256971234', PhoneNormalizer::normalize('6281256971234'));
    }

    /** @test */
    public function normalizes_spaced_number(): void
    {
        $this->assertSame('6281256971234', PhoneNormalizer::normalize('08 1256 971 234'));
    }

    /** @test */
    public function normalizes_dashed_number(): void
    {
        $this->assertSame('6281256971234', PhoneNormalizer::normalize('0812-5697-1234'));
    }

    /** @test */
    public function returns_null_for_empty(): void
    {
        $this->assertNull(PhoneNormalizer::normalize(''));
        $this->assertNull(PhoneNormalizer::normalize(null));
        $this->assertNull(PhoneNormalizer::normalize('   '));
    }

    /** @test */
    public function whatsapp_url_generates_correct_link(): void
    {
        $url = WhatsApp::url('081256971234');
        $this->assertSame('https://wa.me/6281256971234', $url);
    }

    /** @test */
    public function whatsapp_url_with_message(): void
    {
        $url = WhatsApp::url('081256971234', 'Halo, saya ingin bertanya.');
        $this->assertStringStartsWith('https://wa.me/6281256971234?text=', $url);
        $this->assertStringContainsString('Halo', $url);
    }

    /** @test */
    public function whatsapp_url_returns_null_for_invalid(): void
    {
        $this->assertNull(WhatsApp::url(''));
        $this->assertNull(WhatsApp::url(null));
        $this->assertNull(WhatsApp::url('123')); // too short
    }

    /** @test */
    public function whatsapp_url_valid_with_plus_62(): void
    {
        $url = WhatsApp::url('+62 812-3456-7890');
        $this->assertSame('https://wa.me/6281234567890', $url);
    }

    /** @test */
    public function whatsapp_validates_minimum_length(): void
    {
        $this->assertTrue(WhatsApp::isValid('6281234567890'));
        $this->assertFalse(WhatsApp::isValid('62123')); // too short
        $this->assertFalse(WhatsApp::isValid(null));
    }
}
