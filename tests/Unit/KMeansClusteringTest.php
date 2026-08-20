<?php

namespace Tests\Unit;

use App\Services\KMeansClusteringService;
use PHPUnit\Framework\TestCase;

class KMeansClusteringTest extends TestCase
{
    public function test_kmeans_mengelompokkan_data_ke_tiga_klaster_berdasarkan_skor(): void
    {
        $service = new KMeansClusteringService();

        $records = [
            ['id' => 1, 'skor_iks' => 0.90, 'skor_ike' => 0.88, 'skor_ikl' => 0.89, 'skor_komposit' => 0.90],
            ['id' => 2, 'skor_iks' => 0.91, 'skor_ike' => 0.87, 'skor_ikl' => 0.90, 'skor_komposit' => 0.89],
            ['id' => 3, 'skor_iks' => 0.92, 'skor_ike' => 0.86, 'skor_ikl' => 0.88, 'skor_komposit' => 0.91],
            ['id' => 4, 'skor_iks' => 0.76, 'skor_ike' => 0.74, 'skor_ikl' => 0.75, 'skor_komposit' => 0.75],
            ['id' => 5, 'skor_iks' => 0.77, 'skor_ike' => 0.73, 'skor_ikl' => 0.74, 'skor_komposit' => 0.74],
            ['id' => 6, 'skor_iks' => 0.75, 'skor_ike' => 0.72, 'skor_ikl' => 0.76, 'skor_komposit' => 0.76],
            ['id' => 7, 'skor_iks' => 0.50, 'skor_ike' => 0.48, 'skor_ikl' => 0.49, 'skor_komposit' => 0.49],
            ['id' => 8, 'skor_iks' => 0.51, 'skor_ike' => 0.47, 'skor_ikl' => 0.50, 'skor_komposit' => 0.50],
            ['id' => 9, 'skor_iks' => 0.49, 'skor_ike' => 0.46, 'skor_ikl' => 0.48, 'skor_komposit' => 0.48],
            ['id' => 10, 'skor_iks' => 0.18, 'skor_ike' => 0.20, 'skor_ikl' => 0.19, 'skor_komposit' => 0.17],
            ['id' => 11, 'skor_iks' => 0.16, 'skor_ike' => 0.21, 'skor_ikl' => 0.20, 'skor_komposit' => 0.18],
            ['id' => 12, 'skor_iks' => 0.17, 'skor_ike' => 0.22, 'skor_ikl' => 0.18, 'skor_komposit' => 0.16],
        ];

        $clusters = $service->cluster($records, 3);

        $this->assertCount(3, $clusters);
        $this->assertTrue(collect($clusters)->every(fn ($cluster) => count($cluster['members']) >= 1));

        $centroidIdm = collect($clusters)->map(fn ($cluster) => $cluster['centroid']['idm'])->values();
        $this->assertGreaterThan($centroidIdm[3], $centroidIdm[0]);
    }
}
