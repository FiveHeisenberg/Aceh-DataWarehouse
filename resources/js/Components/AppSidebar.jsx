import { Users, HeartHandshake, Activity, GraduationCap, Landmark } from 'lucide-react'
import { usePage } from '@inertiajs/react'
import { cn } from '@/lib/utils'

const navItems = [
    { label: 'Penduduk', icon: Users },
    { label: 'Sosial', icon: HeartHandshake },
    { label: 'Kesehatan', icon: Activity },
    { label: 'Pendidikan', icon: GraduationCap },
    { label: 'Pendapatan Daerah', icon: Landmark, active: true },
]

const dispendaSubItems = [
    { label: 'Ringkasan Pendapatan', href: '/Dispenda' },
    { label: 'Data Tagihan', href: '/Dispenda/tagihan' },
    { label: 'Objek Pajak', href: '/Dispenda/objek-pajak' },
]

export function AppSidebar() {
    const { url } = usePage()
    return (
        <aside className="flex w-72 shrink-0 flex-col border-r border-sidebar-border bg-sidebar">
            <div className="flex items-center gap-3 px-6 py-6">
                <div className="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary text-lg font-bold text-primary-foreground">
                    A
                </div>
                <div className="min-w-0">
                    <p className="truncate text-sm font-bold text-sidebar-foreground">Aceh Data Warehouse</p>
                    <p className="truncate text-xs text-muted-foreground">Provinsi Aceh</p>
                </div>
            </div>

            <nav className="flex flex-col gap-1 px-4 pt-2" aria-label="Navigasi utama">
                {navItems.map((item) => {
                    const Icon = item.icon
                    return (
                        <div key={item.label}>
                            <a
                                href="#"
                                aria-current={item.active ? 'page' : undefined}
                                className={cn(
                                    'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                                    item.active
                                        ? 'border-l-4 border-primary bg-accent text-accent-foreground'
                                        : 'border-l-4 border-transparent text-sidebar-foreground hover:bg-muted'
                                )}
                            >
                                <Icon className="size-[18px] shrink-0" aria-hidden="true" />
                                {item.label}
                            </a>

                            {item.active && (
                                <ul className="mb-1 mt-1 flex flex-col gap-0.5 pl-11" aria-label="Sub-menu Dispenda">
                                    {dispendaSubItems.map((sub) => {
                                        const isActive = url.startsWith(sub.href) && sub.href !== '#'
                                        return (
                                            <li key={sub.label}>
                                                <a
                                                    href={sub.href}
                                                    aria-current={isActive ? 'page' : undefined}
                                                    className={cn(
                                                        'flex rounded-md px-3 py-2 text-sm transition-colors',
                                                        isActive
                                                            ? 'bg-accent/60 font-medium text-accent-foreground'
                                                            : 'text-muted-foreground hover:bg-muted hover:text-sidebar-foreground'
                                                    )}
                                                >
                                                    {sub.label}
                                                </a>
                                            </li>
                                        )
                                    })}
                                </ul>
                            )}
                        </div>
                    )
                })}
            </nav>

            <div className="mt-auto px-6 py-5">
                <p className="text-xs text-muted-foreground">Badan Pengelolaan Keuangan Aceh</p>
                <p className="text-xs text-muted-foreground/70">v2026.1</p>
            </div>
        </aside>
    )
}
