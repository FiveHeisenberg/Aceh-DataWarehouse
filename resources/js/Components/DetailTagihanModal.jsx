import { X } from 'lucide-react'
import { formatRupiah, cn } from '@/lib/utils'
import { useEffect, useState } from 'react'

export function DetailTagihanModal({ isOpen, onClose, tagihanId }) {
    const [detail, setDetail] = useState(null)
    const [loading, setLoading] = useState(false)
    const [error, setError] = useState(null)

    useEffect(() => {
        if (isOpen && tagihanId) {
            setLoading(true)
            setError(null)
            fetch(`/Dispenda/tagihan/${tagihanId}`)
                .then((res) => {
                    if (!res.ok) throw new Error('Gagal memuat data')
                    return res.json()
                })
                .then((data) => {
                    setDetail(data)
                    setLoading(false)
                })
                .catch((err) => {
                    setError(err.message)
                    setLoading(false)
                })
        }
    }, [isOpen, tagihanId])

    useEffect(() => {
        if (isOpen) {
            document.body.style.overflow = 'hidden'
        } else {
            document.body.style.overflow = 'unset'
        }
        return () => {
            document.body.style.overflow = 'unset'
        }
    }, [isOpen])

    if (!isOpen) return null

    function formatTanggal(date) {
        if (!date) return '-'
        return new Date(date).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        })
    }

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" onClick={onClose}>
            <div
                className="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-lg border border-border bg-card shadow-lg"
                onClick={(e) => e.stopPropagation()}
            >
                <div className="sticky top-0 z-10 flex items-center justify-between border-b border-border bg-card px-6 py-4">
                    <h2 className="text-xl font-bold text-foreground">Detail Tagihan Pajak</h2>
                    <button
                        onClick={onClose}
                        className="rounded-lg p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                    >
                        <X className="size-5" />
                    </button>
                </div>

                <div className="p-6">
                    {loading && (
                        <div className="flex items-center justify-center py-12">
                            <div className="size-8 animate-spin rounded-full border-4 border-teal-600 border-t-transparent"></div>
                        </div>
                    )}

                    {error && (
                        <div className="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            {error}
                        </div>
                    )}

                    {!loading && !error && detail && (
                        <div className="space-y-6">
                            <div className="rounded-lg border border-border bg-muted/30 p-4">
                                <div className="mb-3 flex items-center justify-between">
                                    <h3 className="text-sm font-semibold uppercase tracking-wider text-muted-foreground">
                                        Informasi Tagihan
                                    </h3>
                                    <span
                                        className={cn(
                                            'inline-flex items-center rounded-full px-3 py-1 text-xs font-medium',
                                            detail.status_tagihan === 'Lunas'
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-red-100 text-red-700'
                                        )}
                                    >
                                        {detail.status_tagihan}
                                    </span>
                                </div>
                                <div className="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <p className="text-xs text-muted-foreground">ID Tagihan</p>
                                        <p className="mt-1 font-mono text-sm font-medium text-foreground">
                                            {detail.id_tagihan}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-xs text-muted-foreground">Tahun Pajak</p>
                                        <p className="mt-1 text-sm font-medium text-foreground">{detail.tahun_pajak}</p>
                                    </div>
                                    <div>
                                        <p className="text-xs text-muted-foreground">Nominal Tagihan</p>
                                        <p className="mt-1 text-lg font-bold text-foreground">
                                            {formatRupiah(detail.nominal_tagihan)}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-xs text-muted-foreground">Jatuh Tempo</p>
                                        <p className="mt-1 text-sm font-medium text-foreground">
                                            {formatTanggal(detail.tanggal_jatuh_tempo)}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div className="rounded-lg border border-border bg-background p-4">
                                <h3 className="mb-3 text-sm font-semibold uppercase tracking-wider text-muted-foreground">
                                    Wajib Pajak
                                </h3>
                                <div className="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <p className="text-xs text-muted-foreground">NIK</p>
                                        <p className="mt-1 font-mono text-sm font-medium text-foreground">
                                            {detail.nik_wp || '-'}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-xs text-muted-foreground">NPWPD</p>
                                        <p className="mt-1 font-mono text-sm font-medium text-foreground">
                                            {detail.npwpd || '-'}
                                        </p>
                                    </div>
                                    <div className="md:col-span-2">
                                        <p className="text-xs text-muted-foreground">Nama Lengkap</p>
                                        <p className="mt-1 text-sm font-medium text-foreground">{detail.nama_wp}</p>
                                    </div>
                                    <div className="md:col-span-2">
                                        <p className="text-xs text-muted-foreground">Alamat</p>
                                        <p className="mt-1 text-sm text-foreground">{detail.alamat || '-'}</p>
                                    </div>
                                    <div>
                                        <p className="text-xs text-muted-foreground">Kabupaten/Kota</p>
                                        <p className="mt-1 text-sm font-medium text-foreground">
                                            {detail.nama_kabupaten_kota?.replace(/^(Kabupaten |Kota )/i, '') || '-'}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {(detail.nama_pajak || detail.rincian_objek) && (
                                <div className="rounded-lg border border-border bg-background p-4">
                                    <h3 className="mb-3 text-sm font-semibold uppercase tracking-wider text-muted-foreground">
                                        Objek Pajak
                                    </h3>
                                    <div className="grid gap-4 md:grid-cols-2">
                                        {detail.nama_pajak && (
                                            <div>
                                                <p className="text-xs text-muted-foreground">Jenis Pajak</p>
                                                <p className="mt-1 text-sm font-medium text-foreground">
                                                    {detail.nama_pajak}
                                                </p>
                                            </div>
                                        )}
                                        {detail.tarif_persentase && (
                                            <div>
                                                <p className="text-xs text-muted-foreground">Tarif</p>
                                                <p className="mt-1 text-sm font-medium text-foreground">
                                                    {detail.tarif_persentase}%
                                                </p>
                                            </div>
                                        )}
                                        {detail.nomor_identitas_aset && (
                                            <div>
                                                <p className="text-xs text-muted-foreground">Nomor Identitas Aset</p>
                                                <p className="mt-1 font-mono text-sm font-medium text-foreground">
                                                    {detail.nomor_identitas_aset}
                                                </p>
                                            </div>
                                        )}
                                        {detail.nilai_aset && (
                                            <div>
                                                <p className="text-xs text-muted-foreground">Nilai Aset</p>
                                                <p className="mt-1 text-sm font-medium text-foreground">
                                                    {formatRupiah(detail.nilai_aset)}
                                                </p>
                                            </div>
                                        )}
                                        {detail.rincian_objek && (
                                            <div className="md:col-span-2">
                                                <p className="text-xs text-muted-foreground">Rincian Objek</p>
                                                <p className="mt-1 text-sm text-foreground">{detail.rincian_objek}</p>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            )}
                        </div>
                    )}
                </div>

                <div className="sticky bottom-0 border-t border-border bg-card px-6 py-4">
                    <button
                        onClick={onClose}
                        className="w-full rounded-lg bg-muted px-4 py-2.5 text-sm font-medium text-foreground transition-colors hover:bg-muted/80"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    )
}
