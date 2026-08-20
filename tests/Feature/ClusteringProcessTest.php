<?php

namespace Tests\Feature;

use App\Models\DataIDM;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClusteringProcessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->post(route('clustering.process'));
        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_role_gets_redirected_or_forbidden()
    {
        $user = User::factory()->create([
            'role' => 'desa',
            'is_active' => true,
        ]);

        // When a 'desa' user tries to process clustering, they should get access denied / redirected
        $response = $this->actingAs($user)
            ->post(route('clustering.process'));
        
        $response->assertStatus(403);
    }

    public function test_kabupaten_user_can_process_clustering()
    {
        $user = User::factory()->create([
            'role' => 'kabupaten',
            'is_active' => true,
        ]);

        // Create verified IDM records for clustering
        DataIDM::create([
            'nama_desa' => 'Desa Unggul',
            'skor_iks' => 0.90,
            'skor_ike' => 0.88,
            'skor_ikl' => 0.89,
            'skor_komposit' => 0.90,
            'status' => 'mandiri',
            'verifikasi_status' => 'verified',
            'tahun' => 2025,
        ]);

        DataIDM::create([
            'nama_desa' => 'Desa Potensial',
            'skor_iks' => 0.76,
            'skor_ike' => 0.74,
            'skor_ikl' => 0.75,
            'skor_komposit' => 0.75,
            'status' => 'maju',
            'verifikasi_status' => 'verified',
            'tahun' => 2025,
        ]);

        DataIDM::create([
            'nama_desa' => 'Desa Berkembang',
            'skor_iks' => 0.60,
            'skor_ike' => 0.62,
            'skor_ikl' => 0.61,
            'skor_komposit' => 0.61,
            'status' => 'berkembang',
            'verifikasi_status' => 'verified',
            'tahun' => 2025,
        ]);

        DataIDM::create([
            'nama_desa' => 'Desa Rendah',
            'skor_iks' => 0.45,
            'skor_ike' => 0.47,
            'skor_ikl' => 0.44,
            'skor_komposit' => 0.45,
            'status' => 'tertinggal',
            'verifikasi_status' => 'verified',
            'tahun' => 2025,
        ]);

        $response = $this->actingAs($user)
            ->from(route('analisis'))
            ->post(route('clustering.process'), [
                'tahun' => 2025,
            ]);

        $response->assertRedirect(route('analisis'));
        $response->assertSessionHas('success', 'Proses Clustering K-Means berhasil diselesaikan!');

        // Check if database was updated with the correct cluster labels
        $this->assertDatabaseHas('data_idm_desa', [
            'nama_desa' => 'Desa Unggul',
            'cluster' => 'Unggul',
        ]);

        $this->assertDatabaseHas('data_idm_desa', [
            'nama_desa' => 'Desa Potensial',
            'cluster' => 'Potensial',
        ]);

        $this->assertDatabaseHas('data_idm_desa', [
            'nama_desa' => 'Desa Berkembang',
            'cluster' => 'Berkembang',
        ]);

        $this->assertDatabaseHas('data_idm_desa', [
            'nama_desa' => 'Desa Rendah',
            'cluster' => 'Berkembang',
        ]);
    }
}
