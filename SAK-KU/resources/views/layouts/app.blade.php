<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Dynamic Title -->
    <title>@yield('title', 'SAK-KU')</title>
    
    <!-- Global Scripts (e.g., Feather Icons) -->
    <script src="https://unpkg.com/feather-icons"></script>
    
    <!-- External library scripts needed in the head (e.g., Chart.js) -->
    @stack('head-scripts')
    
    <!-- Page Specific CSS -->
    @stack('styles')
</head>
<body class="{{ request()->cookie('theme_mode', 'light') === 'dark' ? 'dark-mode' : '' }}">
    <script>
        (function() {
            const savedTheme = localStorage.getItem("sakku-theme");
            const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");
            if (savedTheme === "dark" || (!savedTheme && prefersDarkScheme.matches)) {
                document.body.classList.add("dark-mode");
            }
        })();
    </script>

    <!-- This slot is where your individual pages will inject their UI -->
    @yield('content')

    <!-- Initialize Feather Icons globally after the DOM loads -->
    <script>
        feather.replace();
    </script>
    
    <!-- Page Specific JavaScript -->
    @stack('scripts')
</body>
</html>