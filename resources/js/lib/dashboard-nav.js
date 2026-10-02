import { router } from '@inertiajs/react'

const DASHBOARD_PATH = '/Dispenda'

export function visitDashboard(params) {
    const current = Object.fromEntries(new URLSearchParams(window.location.search))

    router.get(DASHBOARD_PATH, { ...current, ...params }, { preserveState: true, preserveScroll: true })
}
