import { useState, useEffect } from 'react'
import { router } from '@inertiajs/react'
import { AppSidebar } from '@/Components/AppSidebar'
import { DetailObjekPajakModal } from '@/Components/Dispenda/DetailObjekPajakModal'
import { formatRupiah, cn } from '@/lib/utils'
import { Search, Download, Building2, TrendingUp, DollarSign, Eye } from 'lucide-react'
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell, Legend } from 'recharts'

const CHART_COLORS = ['#0D9488', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6']

function EmptyChartState({ children }) {
    return (
        <div className="flex h-[250px] flex-col items-center justify-center gap-1 px-6 text-center">
            <p className="text-sm font-medium text-foreground">{children}</p>
            <p className="max-w-[300px] text-xs text-muted-foreground">
                Longgarkan filter di Action Bar untuk melihat grafik.
            </p>
        </div>
    )
}

export default function ObjekPajak({ objekPajak, filters, kategoriOptions, wilayahOptions, statistics, chartData }) {
    const [searchValue, setSearchValue] = useState(filters.search)
    const [modalOpen, setModalOpen] = useState(false)
    const [selectedObjekId, setSelectedObjekId] = useState(null)

    useEffect(() => {
        const timer = setTimeout(() => {
            if (searchValue !== filters.search) {
                handleFilterChange({ search: searchValue })
            }
        }, 500)

        return () => clearTimeout(timer)
    }, [searchValue])

    function handleFilterChange(newFilters) {
        router.get('/Dispenda/objek-pajak', { ...filters, ...newFilters }, { preserveState: true, preserveScroll: true })
    }

    function handleDetailClick(idObjek) {
        setSelectedObjekId(idObjek)
        setModalOpen(true)
    }

    function handleExport() {
        const params = new URLSearchParams({
            search: filters.search || '',
            kategori: filters.kategori || 'Semua',
            kabupaten: filters.kabupaten || 'Semua',
            status: filters.status || 'Semua',
        })
        window.location.href = `/Dispenda/objek-pajak-export?${params.toString()}`
    }

    return (
        <div className="flex min-h-screen bg-background">
            <AppSidebar />

            <DetailObjekPajakModal isOpen={modalOpen} onClose={() => setModalOpen(false)} objekId={selectedObjekId} />

            <main className="flex-1 overflow-x-hidden px-6 py-8 md:px-10">
                <div className="mx-auto flex max-w-[1400px] flex-col gap-6">
                    <div className="mb-2 text-sm text-muted-foreground">
                        <span className="font-medium text-teal-600">Pendapatan Daerah</span> / Objek Pajak
                    </div>

                    <div className="mb-4">
                        <h1 className="text-2xl font-bold tracking-tight text-foreground md:text-3xl">
                            Master Data Objek Pajak
                        </h1>
                        <p className="mt-1.5 text-sm text-muted-foreground">
                            Kelola dan pantau aset objek pajak daerah
                        </p>
                    </div>

                    <div className="grid gap-4 lg:grid-cols-3">
                        <div className="flex items-center gap-4 rounded-lg border border-border bg-card p-5 shadow-sm">
                            <div className="flex size-11 shrink-0 items-center justify-center rounded-full bg-blue-100">
                                <Building2 className="size-5 text-blue-700" />
                            </div>
                            <div className="min-w-0">
                                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                    Total Objek Pajak
                                </p>
                                <p className="mt-0.5 text-2xl font-bold text-foreground">
                                    {statistics?.totalObjek?.toLocaleString('id-ID') ?? 0}
                                </p>
                            </div>
                        </div>

                        <div className="flex items-center gap-4 rounded-lg border border-border bg-card p-5 shadow-sm">
                            <div className="flex size-11 shrink-0 items-center justify-center rounded-full bg-green-100">
                                <DollarSign className="size-5 text-green-700" />
                            </div>
                            <div className="min-w-0">
                                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                    Total Potensi Pajak
                                </p>
                                <p className="mt-0.5 truncate text-2xl font-bold text-foreground">
                                    {formatRupiah(statistics?.totalPotensi ?? 0)}
                                </p>
                            </div>
                        </div>

                        <div className="flex min-w-0 flex-col gap-4 rounded-lg border border-border bg-card p-5 shadow-sm">
                            <div className="flex items-center gap-4">
                                <div className="flex size-11 shrink-0 items-center justify-center rounded-full bg-purple-100">
                                    <TrendingUp className="size-5 text-purple-700" />
                                </div>
                                <div className="min-w-0">
                                    <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                        Pertumbuhan Objek
                                    </p>
                                    <p className="mt-0.5 text-2xl font-bold text-foreground">
                                        {statistics?.pertumbuhan > 0 ? '+' : ''}
                                        {statistics?.pertumbuhan ?? 0}%
                                    </p>
                                </div>
                            </div>
                            <p className="text-xs text-muted-foreground">
                                {statistics?.objekTahunIni != null ? (
                                    <>
                                        {statistics.objekTahunIni.toLocaleString('id-ID')} objek masuk pada{' '}
                                        {statistics.tahunAcuan} (tahun berjalan)
                                    </>
                                ) : (
                                    <>Pertumbuhan dibanding tahun sebelumnya</>
                                )}
                            </p>
                        </div>
                    </div>

                    <div className="grid gap-4 lg:grid-cols-2">
                        <div className="rounded-lg border border-border bg-card p-5 shadow-sm">
                            <h3 className="mb-1 text-base font-semibold text-foreground">Tren Pendaftaran Objek</h3>
                            <p className="mb-4 text-xs text-muted-foreground">
                                Jumlah objek per tahun load data
                            </p>
                            {chartData.trendPendaftaran.length === 0 ? (
                                <EmptyChartState>Tidak ada objek pajak yang sesuai filter</EmptyChartState>
                            ) : chartData.trendPendaftaran.length < 2 ? (
                                <div className="flex h-[250px] flex-col items-center justify-center gap-1 px-6 text-center">
                                    <p className="text-sm font-medium text-foreground">Data historis belum tersedia</p>
                                    <p className="max-w-[300px] text-xs text-muted-foreground">
                                        Seluruh objek ({statistics?.totalObjek?.toLocaleString('id-ID')} data) tercatat
                                        pada tahun {chartData.trendPendaftaran[0]?.tahun ?? '—'} saja, sehingga tren
                                        tahunan belum dapat ditampilkan.
                                    </p>
                                </div>
                            ) : (
                                <ResponsiveContainer width="100%" height={250}>
                                    <BarChart data={chartData.trendPendaftaran}>
                                        <CartesianGrid strokeDasharray="3 3" />
                                        <XAxis dataKey="tahun" />
                                        <YAxis />
                                        <Tooltip />
                                        <Bar dataKey="total" fill="#0D9488" radius={[4, 4, 0, 0]} />
                                    </BarChart>
                                </ResponsiveContainer>
                            )}
                        </div>

                        <div className="rounded-lg border border-border bg-card p-5 shadow-sm">
                            <h3 className="mb-1 text-base font-semibold text-foreground">
                                Komposisi Potensi per Kategori
                            </h3>
                            <p className="mb-4 text-xs text-muted-foreground">Distribusi nilai aset objek pajak</p>
                            {chartData.komposisiPotensi.length === 0 ? (
                                <EmptyChartState message="Tidak ada objek pajak yang sesuai filter" />
                            ) : (
                                <ResponsiveContainer width="100%" height={250}>
                                    <PieChart>
                                        <Pie
                                            data={chartData.komposisiPotensi}
                                            dataKey="total"
                                            nameKey="nama_pajak"
                                            cx="50%"
                                            cy="50%"
                                            innerRadius={45}
                                            outerRadius={85}
                                            paddingAngle={2}
                                            label={({ percent }) => `${Math.round(percent * 100)}%`}
                                            labelLine={false}
                                        >
                                            {chartData.komposisiPotensi.map((entry, index) => (
                                                <Cell key={`cell-${index}`} fill={CHART_COLORS[index % CHART_COLORS.length]} />
                                            ))}
                                        </Pie>
                                        <Tooltip
                                            formatter={(value, name) => [formatRupiah(value), name]}
                                            contentStyle={{
                                                backgroundColor: 'var(--popover)',
                                                borderColor: 'var(--border)',
                                                borderRadius: '0.5rem',
                                                fontSize: '0.75rem',
                                            }}
                                        />
                                        <Legend
                                            verticalAlign="bottom"
                                            height={48}
                                            iconType="circle"
                                            wrapperStyle={{ fontSize: '0.75rem' }}
                                        />
                                    </PieChart>
                                </ResponsiveContainer>
                            )}
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center gap-3 rounded-lg border border-border bg-card p-4 shadow-sm">
                        <div className="relative min-w-[250px] flex-1">
                            <Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                            <input
                                type="search"
                                placeholder="Cari ID Objek, Pemilik, atau Nomor Aset..."
                                value={searchValue}
                                onChange={(e) => setSearchValue(e.target.value)}
                                className="h-10 w-full rounded-lg border border-border bg-background pl-10 pr-4 text-sm outline-none transition-colors placeholder:text-muted-foreground hover:border-primary/50 focus-visible:ring-2 focus-visible:ring-ring"
                            />
                        </div>

                        <span className="text-sm font-medium text-foreground">Filter:</span>

                        <select
                            value={filters.kategori || 'Semua'}
                            onChange={(e) => handleFilterChange({ kategori: e.target.value })}
                            className="h-10 cursor-pointer rounded-lg border border-border bg-background px-4 text-sm font-medium text-foreground outline-none transition-colors hover:border-primary/50 focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option>Semua</option>
                            {kategoriOptions.map((kategori) => (
                                <option key={kategori.id} value={kategori.id}>
                                    {kategori.name}
                                </option>
                            ))}
                        </select>

                        <select
                            value={filters.kabupaten || 'Semua'}
                            onChange={(e) => handleFilterChange({ kabupaten: e.target.value })}
                            className="h-10 cursor-pointer rounded-lg border border-border bg-background px-4 text-sm font-medium text-foreground outline-none transition-colors hover:border-primary/50 focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option>Semua</option>
                            {wilayahOptions.map((wilayah) => (
                                <option key={wilayah.id} value={wilayah.id}>
                                    {wilayah.name.replace(/^(Kabupaten |Kota )/i, '')}
                                </option>
                            ))}
                        </select>

                        <select
                            value={filters.status || 'Semua'}
                            onChange={(e) => handleFilterChange({ status: e.target.value })}
                            className="h-10 cursor-pointer rounded-lg border border-border bg-background px-4 text-sm font-medium text-foreground outline-none transition-colors hover:border-primary/50 focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option>Semua</option>
                            <option>Aktif</option>
                            <option>Non-Aktif</option>
                        </select>

                        <button
                            onClick={handleExport}
                            className="flex h-10 items-center gap-2 rounded-lg bg-teal-600 px-4 text-sm font-medium text-white transition-colors hover:bg-teal-700"
                        >
                            <Download className="size-4" />
                            Export Data
                        </button>
                    </div>

                    <div className="overflow-hidden rounded-lg border border-border bg-card shadow-sm">
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead className="border-b border-border bg-muted/50">
                                    <tr>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                            ID Objek
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                            Jenis Pajak
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                            Wajib Pajak (Pemilik)
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                            Lokasi/Alamat
                                        </th>
                                        <th className="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                            Potensi
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                            Status
                                        </th>
                                        <th className="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border">
                                    {objekPajak.data.length === 0 ? (
                                        <tr>
                                            <td colSpan="7" className="px-4 py-8 text-center text-sm text-muted-foreground">
                                                Tidak ada data objek pajak
                                            </td>
                                        </tr>
                                    ) : (
                                        objekPajak.data.map((row) => (
                                            <tr key={row.id_objek} className="transition-colors hover:bg-muted/30">
                                                <td className="px-4 py-3 font-mono text-sm font-medium text-foreground">
                                                    {row.id_objek}
                                                </td>
                                                <td className="px-4 py-3 text-sm text-foreground">{row.nama_pajak}</td>
                                                <td className="px-4 py-3 text-sm text-foreground">{row.pemilik}</td>
                                                <td className="max-w-[240px] truncate px-4 py-3 text-sm text-foreground" title={row.alamat || ''}>
                                                    {row.alamat || '-'}
                                                </td>
                                                <td className="px-4 py-3 text-right text-sm font-medium text-foreground">
                                                    {formatRupiah(row.nilai_aset)}
                                                </td>
                                                <td className="px-4 py-3">
                                                    <span className="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                                        Aktif
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 text-center">
                                                    <button
                                                        onClick={() => handleDetailClick(row.id_objek)}
                                                        className="inline-flex items-center gap-1.5 rounded-lg border border-border px-3 py-1.5 text-sm font-medium text-foreground transition-colors hover:bg-muted"
                                                    >
                                                        <Eye className="size-4" />
                                                        Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {objekPajak.data.length > 0 && (
                            <div className="flex items-center justify-between border-t border-border px-4 py-3">
                                <p className="text-sm text-muted-foreground">
                                    Menampilkan {objekPajak.from} - {objekPajak.to} dari {objekPajak.total} data
                                </p>

                                <div className="flex gap-1">
                                    {objekPajak.links.map((link, index) => (
                                        <button
                                            key={index}
                                            onClick={() => link.url && router.get(link.url)}
                                            disabled={!link.url}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                            className={cn(
                                                'min-w-[36px] rounded px-3 py-1.5 text-sm font-medium transition-colors',
                                                link.active
                                                    ? 'bg-teal-600 text-white'
                                                    : link.url
                                                      ? 'border border-border bg-background text-foreground hover:bg-muted'
                                                      : 'cursor-not-allowed text-muted-foreground opacity-50'
                                            )}
                                        />
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </main>
        </div>
    )
}
