<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/favicon.png">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        
        <!-- Preconnect pour performances -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        
        <title inertia>{{ config('app.name', 'GameMaster') }}</title>

        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
        
        <!-- Noscript fallback pour accessibilité -->
        <noscript>
            <div style="padding: 20px; text-align: center; background: #1f2937; color: white;">
                <h1>JavaScript requis</h1>
                <p>GameMaster nécessite JavaScript pour fonctionner. Veuillez l'activer dans votre navigateur.</p>
            </div>
        </noscript>
    </body>
</html>