import { TrendingUp } from "lucide-react"

function KpiCard({ children }: { children: React.ReactNode }) {
  return <div className="rounded-2xl border border-border bg-card p-6 shadow-sm">{children}</div>
}

export function KpiCards() {
  return (
    <div className="grid gap-5 md:grid-cols-2">
      <KpiCard>
        <p className="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Total Pendapatan</p>
        <p className="mt-3 text-3xl font-bold tracking-tight text-foreground md:text-4xl">Rp 12.450.000.000</p>
        <p className="mt-2 text-sm text-muted-foreground">dalam rupiah, tahun 2026</p>
      </KpiCard>

      <KpiCard>
        <p className="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Target Realisasi</p>
        <div className="mt-3 flex items-baseline gap-3">
          <span className="flex items-center gap-1.5 text-3xl font-bold tracking-tight text-foreground md:text-4xl">
            <TrendingUp className="size-6 text-primary" aria-hidden="true" />
            +14.5%
          </span>
          <span className="text-sm font-medium text-muted-foreground">Per Tahun</span>
        </div>
        <p className="mt-2 text-sm text-muted-foreground">dibanding tahun sebelumnya</p>
      </KpiCard>
    </div>
  )
}
