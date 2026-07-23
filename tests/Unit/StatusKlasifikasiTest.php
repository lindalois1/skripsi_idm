<?php

namespace Tests\Unit;

use App\Services\StatusKlasifikasiService;
use PHPUnit\Framework\TestCase;

class StatusKlasifikasiTest extends TestCase
{
    public function test_mengklasifikasikan_skor_ke_status_yang_konsisten(): void
    {
        $service = new StatusKlasifikasiService();

        $this->assertSame('tertinggal', $service->tentukanStatus(0.59));
        $this->assertSame('berkembang', $service->tentukanStatus(0.60));
        $this->assertSame('maju', $service->tentukanStatus(0.70));
        $this->assertSame('mandiri', $service->tentukanStatus(0.80));
    }

    public function test_threshold_tepat_di_batas(): void
    {
        $service = new StatusKlasifikasiService();

        $this->assertSame('tertinggal', $service->tentukanStatus(0.5999));
        $this->assertSame('berkembang', $service->tentukanStatus(0.6));
        $this->assertSame('maju', $service->tentukanStatus(0.7));
        $this->assertSame('mandiri', $service->tentukanStatus(0.8));
    }
}
