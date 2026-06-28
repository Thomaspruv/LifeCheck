import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Theme & color manager — auto/light/dark mode + customizable color palette
 * Priority: DB user setting → localStorage → system preference → light (default)
 */
document.addEventListener('DOMContentLoaded', () => {
    const html = document.documentElement;

    // Read server-passed user settings (set in app.blade.php)
    const userTheme = html.dataset.userTheme || null;
    const userColor = html.dataset.userThemeColor || 'indigo';

    function getSystemTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function applyTheme(theme, animate = false) {
        if (animate) html.classList.add('dark-transition');
        if (theme === 'dark') {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
        // Update meta theme-color
        const meta = document.querySelector('meta[name="theme-color"]');
        if (meta) {
            meta.content = theme === 'dark' ? '#1f2937' : '#4f46e5';
        }
        setTimeout(() => html.classList.remove('dark-transition'), 400);
    }

    function applyColor(color) {
        html.setAttribute('data-theme-color', color || 'indigo');
    }

    /**
     * Resolve the effective theme mode (dark/light) from:
     * 1. DB setting (userTheme) — 'auto', 'light', 'dark'
     * 2. localStorage override (for quick toggle)
     * 3. System preference fallback
     */
    function resolveTheme() {
        const stored = localStorage.getItem('theme');
        if (stored === 'dark' || stored === 'light') return stored;
        // If user saved a non-auto preference in DB, respect it
        if (userTheme === 'dark') return 'dark';
        if (userTheme === 'light') return 'light';
        // 'auto' or no preference → follow system
        return getSystemTheme();
    }

    function resolveColor() {
        const stored = localStorage.getItem('theme_color');
        return stored || userColor || 'indigo';
    }

    // Apply theme & color on load (no animation for initial paint)
    applyTheme(resolveTheme(), false);
    applyColor(resolveColor());

    // Listen for system changes (only when no user/DB override)
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        const stored = localStorage.getItem('theme');
        if (!stored && (userTheme === 'auto' || !userTheme)) {
            applyTheme(getSystemTheme(), true);
        }
    });

    // Expose for Alpine components + toggle button
    window.themeManager = {
        get current() { return resolveTheme(); },
        get isDark() { return resolveTheme() === 'dark'; },
        toggle() {
            // Toggle clears any DB override and uses localStorage
            const next = resolveTheme() === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', next);
            applyTheme(next, true);
        },
        set(theme) {
            if (theme === 'system' || theme === 'auto') {
                localStorage.removeItem('theme');
                applyTheme(getSystemTheme(), true);
            } else {
                localStorage.setItem('theme', theme);
                applyTheme(theme, true);
            }
        },
        setColor(color) {
            localStorage.setItem('theme_color', color);
            applyColor(color);
        },
        get currentColor() { return resolveColor(); },
    };
});

Alpine.start();
