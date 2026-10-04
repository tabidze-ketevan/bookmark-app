{{-- Applies the saved theme before first paint to avoid a flash of the wrong theme. Keep in sync with resources/js/app.js. --}}
<script>
    (() => {
        let theme = 'system';

        try {
            theme = localStorage.getItem('theme') ?? 'system';
        } catch {}

        const isDark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.classList.toggle('dark', isDark);
    })();
</script>
