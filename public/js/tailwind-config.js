// Konfigurasi Tailwind CDN: warna memakai CSS variable agar mode gelap berfungsi.
tailwind.config = {
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
            colors: {
                surface: 'rgb(var(--surface) / <alpha-value>)',
                lowest: 'rgb(var(--lowest) / <alpha-value>)',
                low: 'rgb(var(--low) / <alpha-value>)',
                container: 'rgb(var(--container) / <alpha-value>)',
                high: 'rgb(var(--high) / <alpha-value>)',
                navy: 'rgb(var(--navy) / <alpha-value>)',
                muted: 'rgb(var(--muted) / <alpha-value>)',
                primary: 'rgb(var(--primary) / <alpha-value>)',
                'primary-fixed': 'rgb(var(--primary-fixed) / <alpha-value>)',
                cyan: 'rgb(var(--cyan) / <alpha-value>)',
                outline: 'rgb(var(--outline) / <alpha-value>)',
                line: 'rgb(var(--line) / <alpha-value>)',
            },
            maxWidth: { page: '1200px' },
            height: { nav: '72px' },
            padding: { nav: '72px' },
        }
    }
};
