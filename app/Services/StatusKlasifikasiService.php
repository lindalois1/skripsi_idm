<?php

namespace App\Services;

class StatusKlasifikasiService
{
    public function tentukanStatus($skor): string
    {
        if ($skor >= 0.8) {
            return 'mandiri';
        }

        if ($skor >= 0.7) {
            return 'maju';
        }

        if ($skor >= 0.6) {
            return 'berkembang';
        }

        return 'tertinggal';
    }
}
