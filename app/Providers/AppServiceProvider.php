<?php

namespace App\Providers;

use App\Models\RiwayatUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $notifications = collect();
            $unreadNotifications = 0;

            if (Auth::check() && Schema::hasTable('riwayat_uploads')) {
                $user = Auth::user();
                $query = RiwayatUpload::with('desa')->orderBy('created_at', 'desc');

                if ($user->role === 'desa' && $user->desa_id) {
                    $query->where('desa_id', $user->desa_id);
                } elseif ($user->role === 'kecamatan' && $user->kecamatan_id) {
                    $query->whereHas('desa', function ($q) use ($user) {
                        $q->where('kecamatan_id', $user->kecamatan_id);
                    });
                }

                $notifications = $query->limit(10)->get()->map(function ($item) {
                    $desa = $item->desa?->nama_desa ?? 'Desa terkait';
                    $kecamatan = $item->desa?->kecamatan;
                    $wilayah = $kecamatan ? "{$desa}, {$kecamatan}" : $desa;
                    $statusLabel = match ($item->status) {
                        'menunggu' => 'Menunggu verifikasi',
                        'proses' => 'Sedang diproses',
                        'disetujui' => 'Sudah disetujui',
                        'revisi' => 'Perlu revisi',
                        default => ucfirst((string) $item->status),
                    };

                    return [
                        'message' => "{$wilayah} sudah mengupload {$item->nama_file}.",
                        'detail' => $item->keterangan ?: 'Upload data IDM masuk ke sistem.',
                        'status' => $item->status,
                        'status_label' => $statusLabel,
                        'time' => $item->created_at?->diffForHumans() ?? '-',
                    ];
                });

                $unreadNotifications = $notifications->count();
            }

            $view->with('uploadNotifications', $notifications)
                 ->with('unreadNotifications', $unreadNotifications);
        });
    }
}
