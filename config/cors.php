<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    | Autoriser le frontend (Vercel / Netlify / GitHub Pages) à appeler l'API
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:3000'),
        'http://localhost',
        'http://127.0.0.1',
        'http://localhost:5500',
        'http://127.0.0.1:5500',
        'http://localhost:3000',
        // Ajoutez votre domaine Vercel/Netlify ici :
        'https://formcook.netlify.app',
    ],

    'allowed_origins_patterns' => [
        // Autoriser tous les sous-domaines Vercel pour le déploiement preview
        '#^https://.*\.vercel\.app$#',
        '#^https://.*\.netlify\.app$#',
        '#^https://.*\.github\.io$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'Content-Disposition', // Pour le téléchargement CSV
    ],

    'max_age' => 86400,

    'supports_credentials' => false,

];