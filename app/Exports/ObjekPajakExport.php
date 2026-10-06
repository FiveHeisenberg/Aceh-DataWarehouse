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

class ObjekPajakExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $search;

    protected $kategori;

    protected $kabupaten;

    protected $status;

    public function __construct($search = '', $kategori = 'Semua', $kabupaten = 'Semua', $status = 'Semua')
    {
        $this->search = $search;
        $this->kategori = $kategori;
        $this->kabupaten = $kabupaten;
        $this->status = $status;
    }

    public function collection(): Enumerable
    {
        $db = DB::connection('mysql');

        $query = $db->table('dwh.dim_objek_pajak')
            ->join('dwh.dim_wajib_pajak', 'dwh.dim_objek_pajak.nik_wp', '=', 'dwh.dim_wajib_pajak.nik_wp')
            ->join('dwh.dim_kategori_pajak', 'dwh.dim_objek_pajak.id_kategori', '=', 'dwh.dim_kategori_pajak.id_kategori')
            ->leftJoinSub(
                $db->table('dwh.dim_wilayah')->select('id_kabupaten_kota', 'nama_kabupaten_kota')->distinct(),
                'wilayah',
                'dwh.dim_wajib_pajak.id_kabupaten_kota',
                '=',
                'wilayah.id_kabupaten_kota'
            )
            ->select(
                'dwh.dim_objek_pajak.id_objek',
                'dwh.dim_kategori_pajak.nama_pajak',
                'dwh.dim_wajib_pajak.nama_lengkap as pemilik',
                'dwh.dim_wajib_pajak.alamat',
                'wilayah.nama_kabupaten_kota',
                'dwh.dim_objek_pajak.nomor_identitas_aset',
                'dwh.dim_objek_pajak.nilai_aset'
            );

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('dwh.dim_objek_pajak.id_objek', 'like', "%{$this->search}%")
                    ->orWhere('dwh.dim_wajib_pajak.nama_lengkap', 'like', "%{$this->search}%")
                    ->orWhere('dwh.dim_objek_pajak.nomor_identitas_aset', 'like', "%{$this->search}%");
            });
        }

        if ($this->kategori && $this->kategori !== 'Semua') {
            $query->where('dwh.dim_objek_pajak.id_kategori', $this->kategori);
        }

        if ($this->kabupaten && $this->kabupaten !== 'Semua') {
            $query->where('dwh.dim_wajib_pajak.id_kabupaten_kota', $this->kabupaten);
        }

        return $query->orderBy('dwh.dim_objek_pajak.waktu_load', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID Objek',
            'Jenis Pajak',
            'Wajib Pajak (Pemilik)',
            'Alamat',
            'Kabupaten/Kota',
            'Nomor Identitas Aset',
            'Potensi (Nilai Aset)',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id_objek,
            $row->nama_pajak,
            $row->pemilik,
            $row->alamat,
            str_replace(['Kabupaten ', 'Kota '], '', $row->nama_kabupaten_kota ?? ''),
            $row->nomor_identitas_aset,
            $row->nilai_aset,
            'Aktif',
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
        return 'Master Objek Pajak';
    }
}
