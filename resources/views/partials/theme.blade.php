<style>
    html.dark body { background: #0f172a; color: #e2e8f0; }

    /* الخلفيات */
    html.dark .bg-gray-50 { background: #0f172a; }
    html.dark .bg-white { background: #1e293b; }
    html.dark .bg-gray-100 { background: #334155; }
    html.dark .bg-gray-800 { background: #475569; }
    html.dark .bg-blue-50 { background: #1e3a5f; }
    html.dark .bg-blue-100 { background: #1e3a5f; }
    html.dark .bg-green-50, html.dark .bg-green-100 { background: #14532d; }
    html.dark .bg-red-50 { background: #450a0a; }
    html.dark .bg-pink-100 { background: #4a1d3a; }
    html.dark .hover\:bg-gray-50:hover { background: #334155; }

    /* النصوص */
    html.dark .text-gray-800 { color: #e2e8f0; }
    html.dark .text-gray-600 { color: #cbd5e1; }
    html.dark .text-gray-500, html.dark .text-gray-400 { color: #94a3b8; }
    html.dark .text-gray-300 { color: #64748b; }
    html.dark .text-green-700 { color: #86efac; }
    html.dark .text-red-600 { color: #fca5a5; }

    /* الحدود */
    html.dark .border, html.dark .border-b, html.dark .border-t { border-color: #334155; }
    html.dark .border-blue-600 { border-color: #2563eb; }
    html.dark .border-red-300 { border-color: #f87171; }

    /* الحقول */
    html.dark input:not([type=checkbox]):not([type=file]),
    html.dark select,
    html.dark textarea {
        background: #0f172a;
        color: #e2e8f0;
        border-color: #334155;
    }
    html.dark ::placeholder { color: #64748b; }
    html.dark input[type=date], html.dark input[type=datetime-local] { color-scheme: dark; }
</style>

<script>
    (function () {
        var theme = 'dark'; // غيّريها لـ 'light' إذا بدك الافتراضي فاتح
        try { theme = localStorage.getItem('theme') || theme; } catch (e) {}
        document.documentElement.classList.toggle('dark', theme === 'dark');
    })();

    function toggleTheme() {
        var isDark = document.documentElement.classList.toggle('dark');
        try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (e) {}
    }
</script>
