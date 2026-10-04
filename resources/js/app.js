import Alpine from 'alpinejs';

window.Alpine = Alpine;

/*
 * Theme (light / dark / system)
 *
 * The choice is stored in localStorage under "theme"; "system" removes the key and follows the OS setting.
 * An inline script in the page <head> (components/theme-script.blade.php) applies the stored theme before
 * first paint, so this module only has to handle changes.
 */
const THEME_STORAGE_KEY = 'theme';
const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)');

const readStoredTheme = () => {
    try {
        return localStorage.getItem(THEME_STORAGE_KEY) ?? 'system';
    } catch {
        return 'system';
    }
};

const storeTheme = (theme) => {
    try {
        if (theme === 'system') {
            localStorage.removeItem(THEME_STORAGE_KEY);
        } else {
            localStorage.setItem(THEME_STORAGE_KEY, theme);
        }
    } catch {
        // Storage can be unavailable (private mode, blocked site data); the theme still applies for this page.
    }
};

const applyTheme = (theme, { animate = false } = {}) => {
    const root = document.documentElement;
    const isDark = theme === 'dark' || (theme === 'system' && systemPrefersDark.matches);

    if (animate) {
        root.classList.add('theme-transition');
        window.setTimeout(() => root.classList.remove('theme-transition'), 250);
    }

    root.classList.toggle('dark', isDark);
};

Alpine.data('themeToggle', () => ({
    theme: readStoredTheme(),

    init() {
        systemPrefersDark.addEventListener('change', () => {
            if (this.theme === 'system') {
                applyTheme('system', { animate: true });
            }
        });

        // Keep other open tabs in sync.
        window.addEventListener('storage', (event) => {
            if (event.key === THEME_STORAGE_KEY) {
                this.theme = event.newValue ?? 'system';
                applyTheme(this.theme, { animate: true });
            }
        });
    },

    select(theme) {
        this.theme = theme;
        storeTheme(theme);
        applyTheme(theme, { animate: true });
    },
}));

/*
 * Copy-to-clipboard quick action. Falls back to a hidden textarea where the async Clipboard API is
 * unavailable (e.g. plain-http local domains).
 */
Alpine.data('copyUrl', (url) => ({
    copied: false,

    async copy() {
        try {
            await navigator.clipboard.writeText(url);
        } catch {
            const textarea = Object.assign(document.createElement('textarea'), { value: url });
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            textarea.remove();
        }

        this.copied = true;
        window.setTimeout(() => (this.copied = false), 1500);
    },
}));

Alpine.start();

/**
 * Close native <details> dropdowns when clicking outside of them or pressing Escape.
 */
const openDropdowns = () => document.querySelectorAll('details[data-dropdown][open]');

document.addEventListener('click', (event) => {
    openDropdowns().forEach((dropdown) => {
        if (!dropdown.contains(event.target)) {
            dropdown.removeAttribute('open');
        }
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        openDropdowns().forEach((dropdown) => dropdown.removeAttribute('open'));
    }
});
