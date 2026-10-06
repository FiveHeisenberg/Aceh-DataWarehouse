import { AppSidebar } from "@/components/dashboard/app-sidebar"
import { DashboardHeader } from "@/components/dashboard/dashboard-header"
import { KpiCards } from "@/components/dashboard/kpi-cards"
import { RevenueMap } from "@/components/dashboard/revenue-map"
import { TaxTrendChart } from "@/components/dashboard/tax-trend-chart"

export default function Page() {
  return (
    <div className="flex min-h-screen bg-background">
      <AppSidebar />

      <main className="flex-1 overflow-x-hidden px-6 py-8 md:px-10">
        <div className="mx-auto flex max-w-[1400px] flex-col gap-6">
          <DashboardHeader />
          <KpiCards />

          <div className="grid gap-5 lg:grid-cols-[65fr_35fr]">
            <RevenueMap />
            <TaxTrendChart />
          </div>
        </div>
      </main>
    </div>
  )
}
