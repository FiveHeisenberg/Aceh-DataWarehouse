import { useState, useEffect } from 'react'
import { router } from '@inertiajs/react'
import { AppSidebar } from '@/Components/AppSidebar'
import { DetailTagihanModal } from '@/Components/Dispenda/DetailTagihanModal'
import { formatRupiah, cn } from '@/lib/utils'
import { Eye, Search, Download, FileText, CheckCircle2, AlertCircle } from 'lucide-react'

export default function DataTagihan({ tagihan, filters, availableYears, wilayahOptions, kategoriPajakOptions, statistics }) {
    const [searchValue, setSearchValue] = useState(filters.search)
    const [modalOpen, setModalOpen] = useState(false)
    const [selectedTagihanId, setSelectedTagihanId] = useState(null)

    useEffect(() => {
        const timer = setTimeout(() => {
            if (searchValue !== filters.search) {
                handleFilterChange({ search: searchValue })
            }
        }, 500)

        return () => clearTimeout(timer)
    }, [searchValue])

    function handleFilterChange(newFilters) {
        router.get(
            '/Dispenda/tagihan',
            { ...filters, ...newFilters },
            { preserveState: true, preserveScroll: true }
        )
    }

    function formatTanggal(date) {
        if (!date) return '-'
        return new Date(date).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        })
    }

    function handleDetailClick(idTagihan) {
        setSelectedTagihanId(idTagihan)
        setModalOpen(true)
    }

    function handleExport() {
        const params = new URLSearchParams({
            search: filters.search || '',
            status: filters.status || 'Semua',
            tahun: filters.tahun || new Date().getFullYear(),
            kabupaten: filters.kabupaten || 'Semua',
            kategori_pajak: filters.kategori_pajak || 'Semua',
        })
        window.location.href = `/Dispenda/tagihan-export?${params.toString()}`
    }

    return (
        <div className="flex min-h-screen bg-background">
            <AppSidebar />

            <DetailTagihanModal
                isOpen={modalOpen}
                onClose={() => setModalOpen(false)}
                tagihanId={selectedTagihanId}
            />

            <main className="flex-1 overflow-x-hidden px-6 py-8 md:px-10">
                <div className="mx-auto flex max-w-[1400px] flex-col gap-6">
                    <div className="mb-2 text-sm text-muted-foreground">
                        <span className="text-teal-600 font-medium">Pendapatan Daerah</span> / Data Tagihan
                    </div>

                    <div className="mb-4">
                        <h1 className="text-2xl font-bold tracking-tight text-foreground md:text-3xl">
                            Data Tagihan Pajak
                        </h1>
                        <p className="mt-1.5 text-sm text-muted-foreground">
                            Kelola dan pantau tagihan pajak daerah
                        </p>
                    </div>

                    <div className="grid gap-4 lg:grid-cols-3">
                        <div className="flex items-center gap-4 rounded-lg border border-border bg-card p-5 shadow-sm">
                            <div className="flex size-11 shrink-0 items-center justify-center rounded-full bg-muted">
                                <FileText className="size-5 text-muted-foreground" />
                            </div>
                            <div className="min-w-0">
                                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                    Total Seluruh Tagihan
                                </p>
                                <p className="mt-0.5 text-2xl font-bold text-foreground">
                                    {statistics?.totalTagihan ?? 0}
                                </p>
                            </div>
                        </div>

                        <div className="flex items-center gap-4 rounded-lg border border-border bg-card p-5 shadow-sm">
                            <div className="flex size-11 shrink-0 items-center justify-center rounded-full bg-green-100">
                                <CheckCircle2 className="size-5 text-green-700" />
                            </div>
                            <div className="min-w-0">
                                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                    Total Nominal Lunas
                                </p>
                                <p className="mt-0.5 truncate text-2xl font-bold text-foreground">
                                    {formatRupiah(statistics?.totalLunas ?? 0)}
                                </p>
                            </div>
                        </div>

                        <div className="flex items-center gap-4 rounded-lg border border-border bg-card p-5 shadow-sm">
                            <div className="flex size-11 shrink-0 items-center justify-center rounded-full bg-red-100">
                                <AlertCircle className="size-5 text-red-700" />
                            </div>
                            <div className="min-w-0">
                                <p className="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                    Total Nominal Belum Lunas
                                </p>
                                <p className="mt-0.5 truncate text-2xl font-bold text-foreground">
                                    {formatRupiah(statistics?.totalBelumLunas ?? 0)}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center gap-3 rounded-lg border border-border bg-card p-4 shadow-sm">
                        <div className="relative flex-1 min-w-[250px]">
                            <Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                            <input
                                type="search"
                                placeholder="Cari ID Tagihan atau Nama..."
                                value={searchValue}
                                onChange={(e) => setSearchValue(e.target.value)}
                                className="h-10 w-full rounded-lg border border-border bg-background pl-10 pr-4 text-sm outline-none transition-colors placeholder:text-muted-foreground hover:border-primary/50 focus-visible:ring-2 focus-visible:ring-ring"
                            />
                        </div>

                        <span className="text-sm font-medium text-foreground">Filter:</span>

                        <select
                            value={filters.status}
                            onChange={(e) => handleFilterChange({ status: e.target.value })}
                            className="h-10 cursor-pointer rounded-lg border border-border bg-background px-4 text-sm font-medium text-foreground outline-none transition-colors hover:border-primary/50 focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option>Semua</option>
                            <option>Lunas</option>
                            <option>Belum Lunas</option>
                        </select>

                        <select
                            value={filters.tahun}
                            onChange={(e) => handleFilterChange({ tahun: e.target.value })}
                            className="h-10 cursor-pointer rounded-lg border border-border bg-background px-4 text-sm font-medium text-foreground outline-none transition-colors hover:border-primary/50 focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            {availableYears.map((year) => (
                                <option key={year} value={year}>
                                    {year}
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
                            value={filters.kategori_pajak || 'Semua'}
                            onChange={(e) => handleFilterChange({ kategori_pajak: e.target.value })}
                            className="h-10 cursor-pointer rounded-lg border border-border bg-background px-4 text-sm font-medium text-foreground outline-none transition-colors hover:border-primary/50 focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option>Semua</option>
                            {kategoriPajakOptions.map((kategori) => (
                                <option key={kategori.id} value={kategori.id}>
                                    {kategori.name}
                                </option>
                            ))}
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
                                            ID Tagihan
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                            Wajib Pajak
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                            Daerah
                                        </th>
                                        <th className="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                            Nominal
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                            Jatuh Tempo
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
                                    {tagihan.data.length === 0 ? (
                                        <tr>
                                            <td colSpan="7" className="px-4 py-8 text-center text-sm text-muted-foreground">
                                                Tidak ada data tagihan
                                            </td>
                                        </tr>
                                    ) : (
                                        tagihan.data.map((row) => (
                                            <tr key={row.id_tagihan} className="hover:bg-muted/30 transition-colors">
                                                <td className="px-4 py-3 text-sm font-mono font-medium text-foreground">
                                                    {row.id_tagihan}
                                                </td>
                                                <td className="px-4 py-3 text-sm text-foreground">{row.nama_wp}</td>
                                                <td className="px-4 py-3 text-sm text-foreground">
                                                    {row.nama_kabupaten_kota?.replace(/^(Kabupaten |Kota )/i, '')}
                                                </td>
                                                <td className="px-4 py-3 text-right text-sm font-medium text-foreground">
                                                    {formatRupiah(row.nominal_tagihan)}
                                                </td>
                                                <td className="px-4 py-3 text-sm text-foreground">
                                                    {formatTanggal(row.tanggal_jatuh_tempo)}
                                                </td>
                                                <td className="px-4 py-3">
                                                    <span
                                                        className={cn(
                                                            'inline-flex items-center rounded-full px-3 py-1 text-xs font-medium',
                                                            row.status_tagihan === 'Lunas'
                                                                ? 'bg-green-100 text-green-700'
                                                                : 'bg-red-100 text-red-700'
                                                        )}
                                                    >
                                                        {row.status_tagihan}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 text-center">
                                                    <button 
                                                        onClick={() => handleDetailClick(row.id_tagihan)}
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

                        {tagihan.data.length > 0 && (
                            <div className="flex items-center justify-between border-t border-border px-4 py-3">
                                <p className="text-sm text-muted-foreground">
                                    Menampilkan {tagihan.from} - {tagihan.to} dari {tagihan.total} data
                                </p>

                                <div className="flex gap-1">
                                    {tagihan.links.map((link, index) => (
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
