import { AppSidebar } from '@/Components/AppSidebar'
import { DashboardHeader } from '@/Components/Dispenda/DashboardHeader'
import { DetailWilayahTable } from '@/Components/Dispenda/DetailWilayahTable'
import { KpiCards } from '@/Components/Dispenda/KpiCards'
import { RevenueMap } from '@/Components/Dispenda/RevenueMap'
import { TaxTrendChart } from '@/Components/Dispenda/TaxTrendChart'

export default function Dashboard({
    tahun,
    availableYears,
    totalPendapatan,
    targetRealisasi,
    totalTunggakan,
    rasioKepatuhan,
    revenueByRegency,
    taxTrend,
    wilayahId,
    wilayahOptions,
}) {
    return (
        <div className="flex min-h-screen bg-background">
            <AppSidebar />

            <main className="flex-1 overflow-x-hidden px-6 py-8 md:px-10">
                <div className="mx-auto flex max-w-[1400px] flex-col gap-6">
                    <DashboardHeader tahun={tahun} availableYears={availableYears} />
                    <KpiCards
                        totalPendapatan={totalPendapatan}
                        targetRealisasi={targetRealisasi}
                        totalTunggakan={totalTunggakan}
                        rasioKepatuhan={rasioKepatuhan}
                    />

                    <div className="grid gap-5 lg:grid-cols-[65fr_35fr]">
                        <RevenueMap revenueByRegency={revenueByRegency} />
                        <TaxTrendChart taxTrend={taxTrend} wilayahId={wilayahId} wilayahOptions={wilayahOptions} />
                    </div>

                    <DetailWilayahTable data={revenueByRegency} tahun={tahun} />
                </div>
            </main>
        </div>
    )
}
