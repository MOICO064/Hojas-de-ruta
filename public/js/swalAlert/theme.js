function getSwalTheme() {
    const theme = document.documentElement.getAttribute('data-bs-theme');

    if (theme === 'auto') {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        return prefersDark ? 'dark' : 'light';
    }
    return theme; 
}

function swalStyles() {
    const mode = getSwalTheme();
    return {
        background: mode === 'dark' ? '#1E293B' : '#ffffff',
        color: mode === 'dark' ? '#ffffff' : '#000000',

    };
}