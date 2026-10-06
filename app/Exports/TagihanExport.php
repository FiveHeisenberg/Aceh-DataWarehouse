<?php

namespace App\Exports;

use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TagihanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $search;

    protected $status;

    protected $tahun;

    protected $kabupaten;

    protected $kategoriPajak;

    public function __construct($search = '', $status = 'Semua', $tahun = null, $kabupaten = 'Semua', $kategoriPajak = 'Semua')
    {
        $this->search = $search;
        $this->status = $status;
        $this->tahun = $tahun ?? date('Y');
        $this->kabupaten = $kabupaten;
        $this->kategoriPajak = $kategoriPajak;
    }

    public function collection(): Enumerable
    {
        $db = DB::connection('mysql');

        $query = $db->table('dwh.fact_tagihan')
            ->join('dwh.dim_wajib_pajak', 'dwh.fact_tagihan.wajib_pajak_key', '=', 'dwh.dim_wajib_pajak.wajib_pajak_key')
            ->joinSub(
                $db->table('dwh.dim_wilayah')->select('id_kabupaten_kota', 'nama_kabupaten_kota')->distinct(),
                'wilayah',
                'dwh.dim_wajib_pajak.id_kabupaten_kota',
                '=',
                'wilayah.id_kabupaten_kota'
            )
            ->select(
                'dwh.fact_tagihan.id_tagihan',
                'dwh.dim_wajib_pajak.nama_lengkap as nama_wp',
                'wilayah.nama_kabupaten_kota',
                'dwh.fact_tagihan.nominal_tagihan',
                'dwh.fact_tagihan.tanggal_jatuh_tempo',
                'dwh.fact_tagihan.status_tagihan',
                'dwh.fact_tagihan.tahun_pajak'
            );

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('dwh.fact_tagihan.id_tagihan', 'like', "%{$this->search}%")
                    ->orWhere('dwh.dim_wajib_pajak.nama_lengkap', 'like', "%{$this->search}%");
            });
        }

        if ($this->status && $this->status !== 'Semua') {
            $query->where('dwh.fact_tagihan.status_tagihan', $this->status);
        }

        if ($this->tahun) {
            $query->where('dwh.fact_tagihan.tahun_pajak', $this->tahun);
        }

        if ($this->kabupaten && $this->kabupaten !== 'Semua') {
            $query->where('dwh.dim_wajib_pajak.id_kabupaten_kota', $this->kabupaten);
        }

        if ($this->kategoriPajak && $this->kategoriPajak !== 'Semua') {
            $query->where('dwh.fact_tagihan.kategori_pajak_key', $this->kategoriPajak);
        }

        return $query->orderBy('dwh.fact_tagihan.tanggal_jatuh_tempo', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID Tagihan',
            'Wajib Pajak',
            'Kabupaten/Kota',
            'Nominal Tagihan',
            'Jatuh Tempo',
            'Status',
            'Tahun Pajak',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id_tagihan,
            $row->nama_wp,
            str_replace(['Kabupaten ', 'Kota '], '', $row->nama_kabupaten_kota ?? ''),
            $row->nominal_tagihan,
            $row->tanggal_jatuh_tempo ? date('d/m/Y', strtotime($row->tanggal_jatuh_tempo)) : '',
            $row->status_tagihan,
            $row->tahun_pajak,
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0D9488'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Tagihan '.$this->tahun;
    }
}
