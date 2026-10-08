<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ETL kadang memuat 'LUNAS'/'BELUM LUNAS' (campur besar-kecil) dan merusak badge
// di Data Tagihan. Jalankan setelah setiap load: php artisan dwh:normalize-status
Artisan::command('dwh:normalize-status', function () {
    // BINARY wajib: kolasi MySQL CI menganggap 'LUNAS' = 'Lunas', jadi WHERE tanpa
    // BINARY melewatkan semua baris. statement() hanya mengembalikan bool — pakai
    // affectingStatement() untuk jumlah baris.
    $affected = DB::connection('mysql')->affectingStatement(<<<'SQL'
        UPDATE dwh.fact_tagihan
        SET status_tagihan = CASE
            WHEN LOWER(TRIM(status_tagihan)) = 'lunas'       THEN 'Lunas'
            WHEN LOWER(TRIM(status_tagihan)) = 'belum lunas' THEN 'Belum Lunas'
            ELSE status_tagihan
        END
        WHERE LOWER(TRIM(status_tagihan)) IN ('lunas', 'belum lunas')
          AND BINARY status_tagihan <> BINARY CASE
              WHEN LOWER(TRIM(status_tagihan)) = 'lunas'       THEN 'Lunas'
              WHEN LOWER(TRIM(status_tagihan)) = 'belum lunas' THEN 'Belum Lunas'
              ELSE status_tagihan
          END
        SQL);

    $variants = DB::connection('mysql')
        ->select('SELECT BINARY status_tagihan v, COUNT(*) n FROM dwh.fact_tagihan GROUP BY BINARY status_tagihan');

    foreach ($variants as $row) {
        $this->line("  '{$row->v}' => {$row->n}");
    }

    $this->info("{$affected} baris dinormalisasi.");
})->purpose('Normalkan casing dwh.fact_tagihan.status_tagihan setelah load ETL');
