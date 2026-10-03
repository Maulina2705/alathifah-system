import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Swal = Swal;

/**
 * Custom Toast Al-Athifa
 */
const AlAthifaToast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
    customClass: {
        popup: '!rounded-2xl !shadow-xl !border !border-slate-100 !bg-white !text-slate-800 !py-3 !px-4',
        title: '!text-sm !font-bold !text-slate-800',
    }
});

window.toastSuccess = (msg) => AlAthifaToast.fire({ icon: 'success', title: msg });
window.toastError = (msg) => AlAthifaToast.fire({ icon: 'error', title: msg });
window.toastInfo = (msg) => AlAthifaToast.fire({ icon: 'info', title: msg });
window.toastWarning = (msg) => AlAthifaToast.fire({ icon: 'warning', title: msg });

/**
 * Custom Alert Dialog Al-Athifa
 */
window.showAlertDialog = function({
    title = 'Informasi',
    text = '',
    icon = 'info',
    confirmButtonText = 'Tutup',
    html = null
}) {
    return Swal.fire({
        title: title,
        text: html ? undefined : text,
        html: html || undefined,
        icon: icon,
        confirmButtonText: confirmButtonText,
        buttonsStyling: false,
        customClass: {
            popup: '!rounded-3xl !p-6 !bg-white !shadow-2xl !border !border-slate-100 !max-w-md',
            title: '!text-lg !font-extrabold !text-slate-800 !tracking-tight',
            htmlContainer: '!text-sm !text-slate-600 !mt-2',
            icon: '!border-0 !my-2',
            actions: '!mt-6 !w-full !flex !justify-end',
            confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-800 hover:bg-slate-900 text-white shadow-md transition'
        }
    });
};

/**
 * Custom Confirmation Dialog Al-Athifa
 */
window.showConfirmDialog = function({
    title = 'Konfirmasi Aksi',
    text = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    icon = 'warning',
    confirmButtonText = 'Ya, Lanjutkan',
    cancelButtonText = 'Batal',
    isDanger = false,
    html = null
}) {
    return Swal.fire({
        title: title,
        text: html ? undefined : text,
        html: html || undefined,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
        reverseButtons: true,
        focusCancel: isDanger,
        buttonsStyling: false,
        backdrop: 'rgba(15, 23, 42, 0.65)',
        customClass: {
            popup: '!rounded-3xl !p-6 !bg-white !shadow-2xl !border !border-slate-100 !max-w-md',
            title: '!text-lg !font-extrabold !text-slate-800 !tracking-tight',
            htmlContainer: '!text-sm !text-slate-600 !mt-2',
            icon: '!border-0 !my-2',
            actions: '!mt-6 !gap-3 !w-full !flex !justify-end',
            confirmButton: isDanger
                ? 'px-5 py-2.5 rounded-xl font-bold text-sm bg-rose-600 hover:bg-rose-700 text-white shadow-lg shadow-rose-600/30 transition cursor-pointer'
                : 'px-5 py-2.5 rounded-xl font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/30 transition cursor-pointer',
            cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer'
        }
    });
};

/**
 * Global Interceptor for Forms with native confirm / data-confirm
 */
document.addEventListener('DOMContentLoaded', () => {
    // Intercept form submission
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.dataset.confirmed === 'true') return;

        const confirmMsg = form.getAttribute('data-confirm');
        const onsubmitAttr = form.getAttribute('onsubmit');
        
        let promptText = confirmMsg;
        let isDelete = false;

        // Check if form is a DELETE action
        const methodInput = form.querySelector('input[name="_method"]');
        if ((methodInput && methodInput.value.toUpperCase() === 'DELETE') || 
            (form.action && form.action.toLowerCase().includes('delete'))) {
            isDelete = true;
        }

        // Detect confirm(...) pattern in onsubmit attribute
        if (!promptText && onsubmitAttr && onsubmitAttr.includes('confirm(')) {
            const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
            if (match && match[1]) {
                promptText = match[1];
            }
        }

        if (promptText) {
            e.preventDefault();
            e.stopImmediatePropagation();

            if (promptText.toLowerCase().includes('hapus') || promptText.toLowerCase().includes('delete')) {
                isDelete = true;
            }

            window.showConfirmDialog({
                title: isDelete ? 'Konfirmasi Penghapusan' : 'Konfirmasi Tindakan',
                text: promptText,
                icon: isDelete ? 'warning' : 'question',
                confirmButtonText: isDelete ? 'Ya, Hapus' : 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                isDanger: isDelete
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    // Strip the onsubmit attribute to avoid triggering again
                    form.removeAttribute('onsubmit');
                    form.submit();
                }
            });
        }
    }, true);

    // Intercept clicks on buttons or links with inline onclick confirm(...)
    document.addEventListener('click', function(e) {
        const target = e.target.closest('button, a');
        if (!target) return;

        const onclickAttr = target.getAttribute('onclick');
        if (onclickAttr && onclickAttr.includes('confirm(')) {
            const match = onclickAttr.match(/confirm\(['"](.*?)['"]\)/);
            if (match && match[1]) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                const promptText = match[1];
                const isDanger = promptText.toLowerCase().includes('hapus') || promptText.toLowerCase().includes('delete') || promptText.toLowerCase().includes('reset');

                window.showConfirmDialog({
                    title: isDanger ? 'Konfirmasi Penghapusan / Reset' : 'Konfirmasi Tindakan',
                    text: promptText,
                    icon: isDanger ? 'warning' : 'question',
                    confirmButtonText: isDanger ? 'Ya, Lanjutkan' : 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    isDanger: isDanger
                }).then((result) => {
                    if (result.isConfirmed) {
                        target.removeAttribute('onclick');
                        target.click();
                        setTimeout(() => target.setAttribute('onclick', onclickAttr), 600);
                    }
                });
            }
        }
    }, true);

    // Intercept clicks on links or buttons with data-confirm-link
    document.addEventListener('click', function(e) {
        const target = e.target.closest('[data-confirm-link]');
        if (!target) return;

        e.preventDefault();
        const text = target.getAttribute('data-confirm-link') || 'Lanjutkan tindakan ini?';
        const href = target.getAttribute('href');
        const isDanger = target.getAttribute('data-confirm-danger') === 'true';

        window.showConfirmDialog({
            title: isDanger ? 'Konfirmasi Penghapusan' : 'Konfirmasi Tindakan',
            text: text,
            icon: isDanger ? 'warning' : 'question',
            confirmButtonText: isDanger ? 'Ya, Hapus' : 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            isDanger: isDanger
        }).then((result) => {
            if (result.isConfirmed && href) {
                window.location.href = href;
            }
        });
    });
});

/**
 * -------------------------------------------------------------
 * Theme Management & Customizer (Light/Dark & Custom Color Palettes)
 * -------------------------------------------------------------
 */
const DEFAULT_PALETTES = {
    classic: {
        name: 'Al-Athifah Klasik (Biru, Pink, Kuning)',
        primary: '#2563eb',
        primaryHover: '#1d4ed8',
        pink: '#ec4899',
        pinkHover: '#db2777',
        yellow: '#f59e0b',
        yellowHover: '#d97706'
    },
    ocean: {
        name: 'Ocean Sapphire & Rose',
        primary: '#0284c7',
        primaryHover: '#0369a1',
        pink: '#f43f5e',
        pinkHover: '#e11d48',
        yellow: '#eab308',
        yellowHover: '#ca8a04'
    },
    indigo: {
        name: 'Royal Indigo & Fuchsia',
        primary: '#4f46e5',
        primaryHover: '#4338ca',
        pink: '#d946ef',
        pinkHover: '#c026d3',
        yellow: '#f59e0b',
        yellowHover: '#d97706'
    },
    cyber: {
        name: 'Cyber Twilight (Navy & Neon)',
        primary: '#1d4ed8',
        primaryHover: '#1e40af',
        pink: '#f43f5e',
        pinkHover: '#be123c',
        yellow: '#facc15',
        yellowHover: '#eab308'
    }
};

window.AlAthifaTheme = {
    // Get stored mode ('light', 'dark', 'auto')
    getMode() {
        return localStorage.getItem('alathifah_theme_mode') || 'light';
    },

    // Set mode
    setMode(mode) {
        localStorage.setItem('alathifah_theme_mode', mode);
        this.applyMode(mode);
        this.updateModeUI(mode);
    },

    // Toggle between light & dark
    toggleMode() {
        const current = this.getMode();
        const next = current === 'dark' ? 'light' : 'dark';
        this.setMode(next);
        if (window.toastInfo) {
            window.toastInfo(`Tema diubah ke mode ${next === 'dark' ? 'Gelap (Dark)' : 'Terang (Light)'}`);
        }
    },

    // Apply class to document
    applyMode(mode) {
        const isDark = mode === 'dark' || (mode === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },

    // Update active state in UI switches
    updateModeUI(mode) {
        document.querySelectorAll('[data-theme-mode-btn]').forEach(btn => {
            const btnMode = btn.getAttribute('data-theme-mode-btn');
            if (btnMode === mode) {
                btn.classList.add('bg-theme-primary', 'text-white', 'shadow-sm');
                btn.classList.remove('text-slate-600', 'dark:text-slate-300');
            } else {
                btn.classList.remove('bg-theme-primary', 'text-white', 'shadow-sm');
                btn.classList.add('text-slate-600', 'dark:text-slate-300');
            }
        });
    },

    // Get custom colors
    getCustomColors() {
        try {
            const raw = localStorage.getItem('alathifah_custom_theme');
            return raw ? JSON.parse(raw) : DEFAULT_PALETTES.classic;
        } catch (e) {
            return DEFAULT_PALETTES.classic;
        }
    },

    // Apply colors to root CSS variables
    applyCustomColors(palette) {
        const root = document.documentElement;
        if (!palette) palette = DEFAULT_PALETTES.classic;

        root.style.setProperty('--theme-primary', palette.primary);
        root.style.setProperty('--theme-primary-hover', palette.primaryHover || palette.primary);
        root.style.setProperty('--theme-primary-light', `${palette.primary}18`);
        root.style.setProperty('--theme-primary-border', `${palette.primary}33`);

        root.style.setProperty('--theme-pink', palette.pink);
        root.style.setProperty('--theme-pink-hover', palette.pinkHover || palette.pink);
        root.style.setProperty('--theme-pink-light', `${palette.pink}18`);
        root.style.setProperty('--theme-pink-border', `${palette.pink}33`);

        root.style.setProperty('--theme-yellow', palette.yellow);
        root.style.setProperty('--theme-yellow-hover', palette.yellowHover || palette.yellow);
        root.style.setProperty('--theme-yellow-light', `${palette.yellow}18`);
        root.style.setProperty('--theme-yellow-border', `${palette.yellow}33`);

        localStorage.setItem('alathifah_custom_theme', JSON.stringify(palette));
    },

    // Apply preset by key
    applyPreset(presetKey) {
        if (DEFAULT_PALETTES[presetKey]) {
            this.applyCustomColors(DEFAULT_PALETTES[presetKey]);
            if (window.toastSuccess) {
                window.toastSuccess(`Preset "${DEFAULT_PALETTES[presetKey].name}" diterapkan!`);
            }
        }
    },

    // Reset to defaults
    reset() {
        localStorage.removeItem('alathifah_custom_theme');
        this.applyCustomColors(DEFAULT_PALETTES.classic);
        if (window.toastInfo) {
            window.toastInfo('Tema berhasil direset ke setelan standar Al-Athifah.');
        }
    },

    // Init on boot
    init() {
        this.applyMode(this.getMode());
        this.applyCustomColors(this.getCustomColors());
        this.updateModeUI(this.getMode());
    }
};

// Auto init theme
window.AlAthifaTheme.init();

