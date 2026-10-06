/**
 * Aceh Data Warehouse - Modul Dispenda / Modal Detail (shared)
 * File: public/js/Dispenda/detail-modal.js
 *
 * Satu renderer generik untuk modal detail Tagihan & Objek Pajak.
 * Konfigurasi field dibaca dari <script type="application/json" id="detail-config">
 * yang di-output Blade, jadi menambah field cukup di Blade tanpa menyentuh JS ini.
 */
(function () {
    'use strict';

    const CFG = document.getElementById('detail-config');
    const elModal = document.getElementById('modal-detail');
    if (!CFG || !elModal || typeof bootstrap === 'undefined') return;

    const config = JSON.parse(CFG.textContent || '{}');
    const elTitle = document.getElementById('modal-detail-title');
    const elBody = document.getElementById('modal-detail-body');
    const modal = bootstrap.Modal.getOrCreateInstance(elModal);

    const rupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    const stripPrefiks = (s) => String(s ?? '').replace(/^(Kabupaten |Kota )\s*/i, '');

    // Escape wajib: data detail berasal dari API publik (alamat, nama, rincian).
    function esc(value) {
        return String(value ?? '').replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        })[c]);
    }

    const TANGGAL = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

    function renderValue(field, raw) {
        if (raw === null || raw === undefined || raw === '' || raw === 0 || raw === '0') {
            return '-';
        }
        switch (field.type) {
            case 'money':   return esc(rupiah(raw));
            case 'mono':    return '<span class="font-monospace">' + esc(raw) + '</span>';
            case 'date':    return esc(TANGGAL.format(new Date(raw)));
            case 'percent': return esc(raw) + '%';
            case 'strip':   return esc(stripPrefiks(raw));
            case 'plain':   return esc(raw);
            default:        return esc(raw);
        }
    }

    function renderSection(section, detail) {
        if (Array.isArray(section.showIfAny) && !section.showIfAny.some((k) => detail[k])) {
            return '';
        }

        let badge = '';
        if (section.status) {
            const ok = detail[section.status.key] === section.status.ok;
            badge = '<span class="badge rounded-pill ' + (ok ? 'badge-lunas' : 'badge-belum') + '">'
                + esc(detail[section.status.key] ?? '') + '</span>';
        } else if (section.statusText) {
            // dim_objek_pajak tidak punya kolom status, jadi badge "Aktif" selalu statis.
            badge = '<span class="badge rounded-pill badge-aktif">' + esc(section.statusText) + '</span>';
        }

        const cells = section.fields
            .filter((f) => !f.showIf || detail[f.key])
            .map((f) =>
                '<div class="' + (f.span ? 'col-12' : 'col-12 col-md-6') + '">'
                + '<p class="modal-field-label">' + esc(f.label) + '</p>'
                + '<p class="modal-field-value">' + renderValue(f, detail[f.key]) + '</p>'
                + '</div>'
            )
            .join('');

        if (!cells) return '';

        return '<div class="border rounded p-3" style="background-color: #f9fafb;">'
            + '<div class="d-flex align-items-center justify-content-between mb-3">'
            + '<h3 class="mb-0 text-uppercase" style="font-size: .75rem; font-weight: 600; letter-spacing: .05em; color: #6b7280;">'
            + esc(section.title) + '</h3>' + badge + '</div>'
            + '<div class="row g-3">' + cells + '</div>'
            + '</div>';
    }

    document.addEventListener('click', async (e) => {
        const trigger = e.target.closest('[data-detail]');
        if (!trigger) return;

        const id = trigger.dataset.detail;
        const url = config.urlTemplate.replace('__ID__', encodeURIComponent(id));

        elTitle.textContent = trigger.dataset.modalTitle || 'Detail';
        elBody.innerHTML = '<div class="d-flex justify-content-center py-5">'
            + '<div class="spinner-border text-success" role="status" style="color: #0d9488 !important;"></div></div>';
        modal.show();

        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Gagal memuat data');

            const detail = await res.json();
            const html = config.sections
                .map((s) => renderSection(s, detail))
                .filter(Boolean)
                .join('');

            elBody.innerHTML = html
                ? '<div class="d-flex flex-column gap-3">' + html + '</div>'
                : '<p class="text-center py-4 mb-0" style="color: #6b7280;">Tidak ada data untuk ditampilkan.</p>';
        } catch (err) {
            elBody.innerHTML = '<div class="border rounded p-3" style="background-color: #fef2f2; border-color: #fecaca !important;">'
                + '<p class="mb-0" style="font-size: .875rem; color: #b91c1c;">' + esc(err.message) + '</p></div>';
        }
    });
})();
