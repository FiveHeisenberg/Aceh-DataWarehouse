// Pendapatan daerah per kabupaten/kota (dalam miliar Rupiah), tahun 2026.
// Nama kunci harus sama persis dengan properti `name` pada /public/aceh-regencies.json.
export const revenueByRegency: Record<string, number> = {
  "Kota Banda Aceh": 1180,
  "Aceh Utara": 1040,
  "Kota Lhokseumawe": 720,
  "Aceh Besar": 690,
  "Bireuen": 640,
  "Pidie": 610,
  "Aceh Timur": 585,
  "Aceh Tamiang": 540,
  "Kota Langsa": 510,
  "Aceh Barat": 470,
  "Nagan Raya": 445,
  "Aceh Selatan": 420,
  "Aceh Tengah": 400,
  "Pidie Jaya": 375,
  "Aceh Tenggara": 350,
  "Aceh Barat Daya": 330,
  "Aceh Jaya": 305,
  "Bener Meriah": 290,
  "Simeulue": 245,
  "Aceh Singkil": 230,
  "Gayo Lues": 205,
  "Kota Subulussalam": 190,
  "Kota Sabang": 175,
}

export const revenueValues = Object.values(revenueByRegency)
export const revenueMin = Math.min(...revenueValues)
export const revenueMax = Math.max(...revenueValues)

export function formatMiliar(value: number): string {
  // value dalam miliar rupiah -> "Rp 1,18 T" atau "Rp 720 M"
  if (value >= 1000) {
    return `Rp ${(value / 1000).toLocaleString("id-ID", { maximumFractionDigits: 2 })} T`
  }
  return `Rp ${value.toLocaleString("id-ID")} M`
}

export const taxTrend = [
  { year: "2024", pajak: 9.2 },
  { year: "2025", pajak: 10.6 },
  { year: "2026", pajak: 12.45 },
]

export const trendScopes = [
  "Seluruh Aceh (total)",
  "Kota Banda Aceh",
  "Aceh Utara",
  "Kota Lhokseumawe",
  "Aceh Besar",
]
