<!-- SweetAlert2 Library (Local bundle with CDN fallback) -->
<script src="{{ asset('sweetalert2.all.min.js') }}"></script>
<script>
    if (typeof Swal === 'undefined') {
        document.write('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>');
    }
</script>

<style>
    /* Styling modern untuk SweetAlert2 */
    .swal2-container {
        backdrop-filter: blur(5px) !important;
        -webkit-backdrop-filter: blur(5px) !important;
        background-color: rgba(15, 23, 42, 0.45) !important;
        z-index: 99999 !important;
    }
    .swal2-popup {
        border-radius: 1.25rem !important; /* rounded-2xl */
        padding: 1.75rem 1.5rem !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        font-family: inherit !important;
        border: 1px solid #f1f5f9 !important;
    }
    .swal2-title {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-bottom: 0.5rem !important;
    }
    .swal2-html-container {
        font-size: 0.9rem !important;
        color: #475569 !important;
        line-height: 1.55 !important;
        margin: 0.5rem 0 1.25rem 0 !important;
    }
    .swal2-actions {
        gap: 0.625rem !important;
        margin-top: 1rem !important;
        width: 100% !important;
        display: flex !important;
        justify-content: center !important;
    }
    .swal2-actions button {
        margin: 0 !important;
    }
    .swal-btn-confirm {
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        padding: 0.625rem 1.25rem !important;
        border-radius: 0.625rem !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.375rem !important;
    }
    .swal-btn-cancel {
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        padding: 0.625rem 1.25rem !important;
        border-radius: 0.625rem !important;
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #e2e8f0 !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
    }
    .swal-btn-cancel:hover {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
    }
</style>

<script>
    // Utility Sanitizer
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Universal App Confirm Helper
    window.appConfirm = function({
        title = 'Konfirmasi',
        text = '',
        html = '',
        icon = 'question',
        confirmText = 'Ya, Lanjutkan',
        confirmBg = '#059669', // Emerald
        cancelText = 'Batal',
        onConfirm = null
    }) {
        if (typeof Swal === 'undefined') {
            if (confirm(text || title)) {
                if (typeof onConfirm === 'function') onConfirm();
            }
            return;
        }

        Swal.fire({
            title: title,
            text: text,
            html: html,
            icon: icon,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            reverseButtons: true,
            focusConfirm: false,
            buttonsStyling: false,
            customClass: {
                confirmButton: 'swal-btn-confirm text-white shadow-sm',
                cancelButton: 'swal-btn-cancel'
            },
            didOpen: () => {
                const btn = Swal.getConfirmButton();
                if (btn) {
                    btn.style.backgroundColor = confirmBg;
                }
            }
        }).then((result) => {
            if (result.isConfirmed && typeof onConfirm === 'function') {
                onConfirm();
            }
        });
    };

    // Helper Khusus: Setujui Peminjaman
    window.confirmSetujui = function(btn, peminjam, detailAlat = '') {
        const form = btn.closest('form');
        const safeName = escapeHtml(peminjam);
        const safeDetail = escapeHtml(detailAlat);

        let detailHtml = '';
        if (safeDetail) {
            detailHtml = `
                <div class="mt-3 p-3 bg-emerald-50/70 border border-emerald-200/60 rounded-xl text-left">
                    <div class="text-xs font-semibold text-emerald-800 uppercase tracking-wider mb-1">Detail Alat yang Dipinjam:</div>
                    <div class="text-xs text-emerald-950 font-medium">${safeDetail}</div>
                </div>
            `;
        }

        appConfirm({
            title: 'Setujui Peminjaman?',
            html: `
                <div>
                    Apakah Anda yakin ingin menyetujui pengajuan peminjaman untuk <strong class="text-gray-900 font-semibold">${safeName}</strong>?
                    ${detailHtml}
                    <div class="mt-3 text-xs text-gray-500">
                        Status peminjaman akan otomatis berubah menjadi <span class="font-semibold text-emerald-700">Dipinjam</span> dan stok alat akan dikurangi.
                    </div>
                </div>
            `,
            icon: 'question',
            confirmText: '✓ Ya, Setujui',
            confirmBg: '#059669', // emerald-600
            cancelText: 'Batal',
            onConfirm: () => form.submit()
        });
    };

    // Helper Khusus: Tolak Peminjaman
    window.confirmTolak = function(btn, peminjam) {
        const form = btn.closest('form');
        const safeName = escapeHtml(peminjam);

        appConfirm({
            title: 'Tolak Peminjaman?',
            html: `
                <div>
                    Apakah Anda yakin ingin menolak pengajuan peminjaman dari <strong class="text-gray-900 font-semibold">${safeName}</strong>?
                    <div class="mt-2 text-xs text-red-600 bg-red-50 p-2.5 rounded-lg border border-red-100 font-medium">
                        ⚠️ Pengajuan yang ditolak tidak dapat diproses kembali.
                    </div>
                </div>
            `,
            icon: 'warning',
            confirmText: '✕ Ya, Tolak',
            confirmBg: '#dc2626', // red-600
            cancelText: 'Batal',
            onConfirm: () => form.submit()
        });
    };

    // Helper Khusus: Hapus Data
    window.confirmHapus = function(btn, namaItem = 'data ini') {
        const form = btn.closest('form');
        const safeItem = escapeHtml(namaItem);

        appConfirm({
            title: 'Hapus Data?',
            html: `
                <div>
                    Apakah Anda yakin ingin menghapus <strong class="text-gray-900 font-semibold">${safeItem}</strong>?
                    <div class="mt-2 text-xs text-red-600 bg-red-50 p-2.5 rounded-lg border border-red-100 font-medium">
                        ⚠️ Data yang sudah dihapus tidak dapat dipulihkan kembali.
                    </div>
                </div>
            `,
            icon: 'warning',
            confirmText: 'Ya, Hapus',
            confirmBg: '#dc2626',
            cancelText: 'Batal',
            onConfirm: () => form.submit()
        });
    };

    // Helper Khusus: Ajukan Pengembalian (Peminjam)
    window.confirmAjukanKembali = function(btn) {
        const form = btn.closest('form');
        appConfirm({
            title: 'Ajukan Pengembalian?',
            html: `
                <div>
                    Apakah Anda ingin mengajukan pengembalian untuk alat ini?
                    <div class="mt-2 text-xs text-amber-700 bg-amber-50 p-2.5 rounded-lg border border-amber-200">
                        📦 Harap segera membawa fisik alat ke meja petugas untuk pemeriksaan kondisi alat.
                    </div>
                </div>
            `,
            icon: 'question',
            confirmText: 'Ya, Ajukan',
            confirmBg: '#ea580c', // orange-600
            cancelText: 'Batal',
            onConfirm: () => form.submit()
        });
    };

    // Intercept semua form dengan data-confirm otomatis
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.dataset.confirmed === 'true') {
                return;
            }
            const confirmMsg = form.getAttribute('data-confirm');
            if (confirmMsg) {
                e.preventDefault();
                const confirmTitle = form.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
                const confirmIcon = form.getAttribute('data-confirm-icon') || 'warning';
                const confirmColor = form.getAttribute('data-confirm-color') || '#dc2626';
                const confirmBtn = form.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';

                appConfirm({
                    title: confirmTitle,
                    text: confirmMsg,
                    icon: confirmIcon,
                    confirmText: confirmBtn,
                    confirmBg: confirmColor,
                    onConfirm: () => {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }
                });
            }
        });
    });

    // Toast Notifikasi Otomatis untuk Session Flash
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: @json(session('success')),
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-xl shadow-lg border border-emerald-100 text-sm font-medium'
                    }
                });
            }
        });
    @endif

    @if(session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: @json(session('error')),
                    showConfirmButton: false,
                    timer: 4500,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-xl shadow-lg border border-red-100 text-sm font-medium'
                    }
                });
            }
        });
    @endif
</script>
