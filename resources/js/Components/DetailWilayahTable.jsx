import { useState } from 'react'
import { Search } from 'lucide-react'
import { formatRupiah } from '@/lib/utils'

export function DetailWilayahTable({ data, tahun }) {
    const [cari, setCari] = useState('')

    const keyword = cari.trim().toLowerCase()
    const rows = (data || []).filter((r) => r.name.toLowerCase().includes(keyword))

    return (
        <div className="rounded-2xl border border-border bg-card p-6 shadow-sm">
            <div className="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 className="text-base font-semibold text-foreground">Detail Kabupaten / Kota</h2>
                    <p className="text-xs text-muted-foreground">{rows.length} baris</p>
                </div>

                <div className="relative w-full sm:w-64">
                    <label htmlFor="wilayah-search" className="sr-only">
                        Cari wilayah
                    </label>
                    <Search
                        className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                        aria-hidden="true"
                    />
                    <input
                        id="wilayah-search"
                        type="search"
                        value={cari}
                        onChange={(e) => setCari(e.target.value)}
                        placeholder="Cari wilayah..."
                        className="h-10 w-full rounded-lg border border-border bg-background pl-9 pr-3 text-sm text-foreground shadow-sm outline-none transition-colors placeholder:text-muted-foreground hover:border-primary/50 focus-visible:ring-2 focus-visible:ring-ring"
                    />
                </div>
            </div>

            <div className="overflow-x-auto">
                <table className="w-full text-sm">
                    <caption className="sr-only">Total pendapatan pajak per kabupaten/kota tahun {tahun}</caption>
                    <thead>
                        <tr className="border-b border-border text-left text-xs uppercase tracking-wider text-muted-foreground">
                            <th scope="col" className="py-3 pr-4 font-semibold">Kabupaten/Kota</th>
                            <th scope="col" className="py-3 pr-4 font-semibold">Tahun</th>
                            <th scope="col" className="py-3 text-right font-semibold">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-border">
                        {rows.length === 0 ? (
                            <tr>
                                <td colSpan={3} className="py-8 text-center text-muted-foreground">
                                    Tidak ada wilayah yang cocok.
                                </td>
                            </tr>
                        ) : (
                            rows.map((row) => (
                                <tr key={row.name} className="transition-colors hover:bg-muted/50">
                                    <td className="py-3 pr-4 font-medium text-foreground">{row.name}</td>
                                    <td className="py-3 pr-4 text-muted-foreground">{tahun}</td>
                                    <td className="py-3 text-right font-medium tabular-nums text-foreground">
                                        {formatRupiah(row.total)}
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    )
}
