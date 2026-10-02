"use client"

import { useState } from "react"
import { ComposableMap, Geographies, Geography, ZoomableGroup } from "react-simple-maps"
import { scaleLinear } from "d3-scale"
import { Maximize2, Plus, Minus } from "lucide-react"
import { revenueByRegency, revenueMin, revenueMax, formatMiliar } from "./data"

const GEO_URL = "/aceh-regencies.json"

// Gradasi hijau muda -> hijau tua (kepadatan pendapatan)
const colorScale = scaleLinear<string>().domain([revenueMin, revenueMax]).range(["#d1fae5", "#0f766e"])

const INITIAL = { coordinates: [96.67, 4.0] as [number, number], zoom: 1 }
const MIN_ZOOM = 1
const MAX_ZOOM = 6

type Tooltip = { name: string; value: number; x: number; y: number } | null

export function RevenueMap() {
  const [position, setPosition] = useState(INITIAL)
  const [tooltip, setTooltip] = useState<Tooltip>(null)

  function zoom(factor: number) {
    setPosition((p) => ({
      ...p,
      zoom: Math.min(MAX_ZOOM, Math.max(MIN_ZOOM, p.zoom * factor)),
    }))
  }

  return (
    <div className="relative flex h-full flex-col rounded-2xl border border-border bg-card p-6 shadow-sm">
      <div className="mb-4 flex items-start justify-between">
        <div>
          <h2 className="text-base font-semibold text-foreground">Sebaran Pendapatan Daerah</h2>
          <p className="text-xs text-muted-foreground">Kepadatan pendapatan per kabupaten/kota</p>
        </div>
        <button
          type="button"
          aria-label="Perbesar peta"
          className="flex size-8 items-center justify-center rounded-lg border border-border text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
        >
          <Maximize2 className="size-4" aria-hidden="true" />
        </button>
      </div>

      <div
        className="relative flex-1 overflow-hidden rounded-xl"
        style={{ backgroundColor: "#e0f2fe", minHeight: 360 }}
        onMouseLeave={() => setTooltip(null)}
      >
        {/* Zoom controls */}
        <div className="absolute left-3 top-3 z-10 flex flex-col overflow-hidden rounded-lg border border-border bg-card shadow-sm">
          <button
            type="button"
            aria-label="Perbesar"
            onClick={() => zoom(1.5)}
            className="flex size-8 items-center justify-center text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
          >
            <Plus className="size-4" aria-hidden="true" />
          </button>
          <div className="h-px bg-border" />
          <button
            type="button"
            aria-label="Perkecil"
            onClick={() => zoom(1 / 1.5)}
            className="flex size-8 items-center justify-center text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
          >
            <Minus className="size-4" aria-hidden="true" />
          </button>
        </div>

        {/* Legend */}
        <div className="absolute bottom-3 left-3 z-10 rounded-lg border border-border bg-card/90 px-3 py-2 shadow-sm backdrop-blur">
          <p className="mb-1 text-[10px] font-medium uppercase tracking-wide text-muted-foreground">Pendapatan</p>
          <div className="flex items-center gap-2">
            <span className="text-[10px] text-muted-foreground">Rendah</span>
            <div
              className="h-2 w-24 rounded-full"
              style={{ background: "linear-gradient(to right, #d1fae5, #0f766e)" }}
            />
            <span className="text-[10px] text-muted-foreground">Tinggi</span>
          </div>
        </div>

        <ComposableMap
          projection="geoMercator"
          projectionConfig={{ center: INITIAL.coordinates, scale: 7000 }}
          width={800}
          height={520}
          style={{ width: "100%", height: "100%" }}
        >
          <ZoomableGroup
            center={position.coordinates}
            zoom={position.zoom}
            minZoom={MIN_ZOOM}
            maxZoom={MAX_ZOOM}
            onMoveEnd={(pos) =>
              setPosition({
                coordinates: (pos.coordinates as [number, number]) ?? INITIAL.coordinates,
                zoom: pos.zoom ?? 1,
              })
            }
          >
            <Geographies geography={GEO_URL}>
              {({ geographies }) =>
                geographies.map((geo) => {
                  const name: string = geo.properties.name
                  const value = revenueByRegency[name] ?? revenueMin
                  return (
                    <Geography
                      key={geo.rsmKey}
                      geography={geo}
                      fill={colorScale(value)}
                      stroke="#ffffff"
                      strokeWidth={0.5}
                      onMouseEnter={(e) =>
                        setTooltip({ name, value, x: e.clientX, y: e.clientY })
                      }
                      onMouseMove={(e) =>
                        setTooltip((t) => (t ? { ...t, x: e.clientX, y: e.clientY } : t))
                      }
                      onMouseLeave={() => setTooltip(null)}
                      style={{
                        default: { outline: "none" },
                        hover: { outline: "none", fill: "#134e4a", cursor: "pointer" },
                        pressed: { outline: "none" },
                      }}
                    />
                  )
                })
              }
            </Geographies>
          </ZoomableGroup>
        </ComposableMap>

        {tooltip && (
          <div
            className="pointer-events-none fixed z-50 rounded-lg border border-border bg-card px-3 py-2 text-xs shadow-lg"
            style={{ left: tooltip.x + 12, top: tooltip.y + 12 }}
          >
            <p className="font-semibold text-foreground">{tooltip.name}</p>
            <p className="text-muted-foreground">{formatMiliar(tooltip.value)}</p>
          </div>
        )}
      </div>
    </div>
  )
}
