"use client"

import { useState } from "react"
import { Area, AreaChart, CartesianGrid, XAxis, YAxis } from "recharts"
import { ChevronDown } from "lucide-react"
import { ChartContainer, ChartTooltip, ChartTooltipContent, type ChartConfig } from "@/components/ui/chart"
import { taxTrend, trendScopes } from "./data"

const chartConfig = {
  pajak: {
    label: "Penerimaan Pajak",
    color: "var(--chart-1)",
  },
} satisfies ChartConfig

export function TaxTrendChart() {
  const [scope, setScope] = useState(trendScopes[0])

  return (
    <div className="flex h-full flex-col rounded-2xl border border-border bg-card p-6 shadow-sm">
      <div className="mb-2">
        <h2 className="text-base font-semibold text-foreground">Tren Penerimaan Pajak</h2>
        <p className="text-xs text-muted-foreground">dalam triliun rupiah</p>
      </div>

      <div className="flex-1">
        <ChartContainer config={chartConfig} className="h-full min-h-[220px] w-full">
          <AreaChart data={taxTrend} margin={{ top: 12, right: 12, left: 4, bottom: 4 }}>
            <defs>
              <linearGradient id="fillPajak" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stopColor="var(--color-pajak)" stopOpacity={0.25} />
                <stop offset="100%" stopColor="var(--color-pajak)" stopOpacity={0.02} />
              </linearGradient>
            </defs>
            <CartesianGrid vertical={false} strokeDasharray="3 3" />
            <XAxis dataKey="year" tickLine={false} axisLine={false} tickMargin={8} />
            <YAxis
              tickLine={false}
              axisLine={false}
              width={28}
              ticks={[0, 4, 8, 12]}
              domain={[0, 13]}
            />
            <ChartTooltip cursor={false} content={<ChartTooltipContent indicator="line" />} />
            <Area
              type="monotone"
              dataKey="pajak"
              stroke="var(--color-pajak)"
              strokeWidth={3}
              fill="url(#fillPajak)"
              dot={{ r: 4, fill: "var(--color-pajak)", strokeWidth: 0 }}
              activeDot={{ r: 6 }}
            />
          </AreaChart>
        </ChartContainer>
      </div>

      <div className="mt-4 border-t border-border pt-4">
        <label htmlFor="trend-scope" className="mb-2 block text-xs font-medium text-muted-foreground">
          Tampilkan tren
        </label>
        <div className="relative">
          <select
            id="trend-scope"
            value={scope}
            onChange={(e) => setScope(e.target.value)}
            className="h-10 w-full cursor-pointer appearance-none rounded-lg border border-border bg-card pl-3 pr-9 text-sm font-medium text-foreground outline-none transition-colors hover:border-primary/50 focus-visible:ring-2 focus-visible:ring-ring"
          >
            {trendScopes.map((s) => (
              <option key={s} value={s}>
                {s}
              </option>
            ))}
          </select>
          <ChevronDown
            className="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
            aria-hidden="true"
          />
        </div>
      </div>
    </div>
  )
}
