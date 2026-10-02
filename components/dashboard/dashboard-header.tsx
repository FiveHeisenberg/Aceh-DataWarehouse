"use client"

import { ChevronDown } from "lucide-react"
import { useState } from "react"

export function DashboardHeader() {
  const [year, setYear] = useState("2026")

  return (
    <header className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
      <div>
        <h1 className="text-2xl font-bold tracking-tight text-foreground text-balance md:text-3xl">
          Data Pendapatan Daerah Provinsi Aceh
        </h1>
        <p className="mt-1.5 text-sm text-muted-foreground">Cakupan data: 2026, 23 kabupaten/kota.</p>
      </div>

      <div className="relative shrink-0">
        <label htmlFor="year-filter" className="sr-only">
          Pilih tahun
        </label>
        <select
          id="year-filter"
          value={year}
          onChange={(e) => setYear(e.target.value)}
          className="h-10 cursor-pointer appearance-none rounded-lg border border-border bg-card pl-4 pr-10 text-sm font-medium text-foreground shadow-sm outline-none transition-colors hover:border-primary/50 focus-visible:ring-2 focus-visible:ring-ring"
        >
          <option value="2026">2026</option>
          <option value="2025">2025</option>
          <option value="2024">2024</option>
        </select>
        <ChevronDown
          className="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
          aria-hidden="true"
        />
      </div>
    </header>
  )
}
