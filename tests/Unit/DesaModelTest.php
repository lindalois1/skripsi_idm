<?php

namespace Tests\Unit;

use App\Models\Desa;
use PHPUnit\Framework\TestCase;

class DesaModelTest extends TestCase
{
    public function test_desa_model_menerima_field_kecamatan_dan_lokasi_lainnya(): void
    {
        $desa = new Desa([
            'nama_desa' => 'Desa Test',
            'kode_desa' => '12345',
            'kecamatan' => 'Kec Test',
            'kabupaten' => 'INDRAMAYU',
            'provinsi' => 'JAWA BARAT',
        ]);

        $this->assertSame('Desa Test', $desa->nama_desa);
        $this->assertSame('Kec Test', $desa->kecamatan);
        $this->assertSame('INDRAMAYU', $desa->kabupaten);
        $this->assertSame('JAWA BARAT', $desa->provinsi);
    }
}
