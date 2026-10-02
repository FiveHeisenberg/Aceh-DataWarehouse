import { clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs) {
    return twMerge(clsx(inputs))
}

export function formatRupiah(value) {
    if (value == null) return 'Rp 0'
    return 'Rp ' + Number(value).toLocaleString('id-ID')
}
