import { TrendingUp, AlertTriangle, Percent } from 'lucide-react'
import { formatRupiah } from '@/lib/utils'

function KpiCard({ children }) {
    return <div className="rounded-2xl border border-border bg-card p-6 shadow-sm">{children}</div>
}

export function KpiCards({ totalPendapatan, targetRealisasi, totalTunggakan, rasioKepatuhan }) {
    return (
        <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-4">
            <KpiCard>
                <p className="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Total Pendapatan</p>
                <p className="mt-3 text-3xl font-bold tracking-tight text-foreground md:text-2xl">
                    {formatRupiah(totalPendapatan)}
                </p>
                <p className="mt-2 text-sm text-muted-foreground">akumulasi pembayaran Lunas</p>
            </KpiCard>

            <KpiCard>
                <p className="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Target Realisasi</p>
                <div className="mt-3 flex items-baseline gap-3">
                    <span className="flex items-center gap-1.5 text-3xl font-bold tracking-tight text-foreground md:text-2xl">
                        <TrendingUp className="size-6 text-primary" aria-hidden="true" />
                        +{targetRealisasi}%
                    </span>
                    <span className="text-sm font-medium text-muted-foreground">Per Tahun</span>
                </div>
                <p className="mt-2 text-sm text-muted-foreground">dibanding tahun sebelumnya</p>
            </KpiCard>

            <KpiCard>
                <p className="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Total Tunggakan</p>
                <div className="mt-3 flex items-baseline gap-3">
                    <span className="flex items-center gap-1.5 text-3xl font-bold tracking-tight text-foreground md:text-2xl">
                        <AlertTriangle className="size-6 text-destructive" aria-hidden="true" />
                        {formatRupiah(totalTunggakan)}
                    </span>
                </div>
                <p className="mt-2 text-sm text-muted-foreground">tagihan berstatus Belum Lunas</p>
            </KpiCard>

            <KpiCard>
                <p className="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Rasio Kepatuhan</p>
                <div className="mt-3 flex items-baseline gap-3">
                    <span className="flex items-center gap-1.5 text-3xl font-bold tracking-tight text-foreground md:text-2xl">
                        <Percent className="size-6 text-primary" aria-hidden="true" />
                        {Number(rasioKepatuhan).toFixed(1)}%
                    </span>
                </div>
                <p className="mt-2 text-sm text-muted-foreground">tagihan Lunas dari total tagihan</p>
            </KpiCard>
        </div>
    )
}
