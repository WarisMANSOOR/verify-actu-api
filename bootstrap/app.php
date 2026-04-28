<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Ce fichier sert à configurer le démarrage de Laravel.
// J'ai surtout ajouté la ligne api pour que Laravel charge bien les routes dans routes/api.php.
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    // Ici Laravel prépare les middlewares de l'application.
    // Pour le moment je ne rajoute rien dedans, car Sanctum fonctionne déjà avec auth:sanctum.
    ->withMiddleware(function (Middleware $middleware): void {
        
    })

    // Ici Laravel permet de personnaliser la gestion des erreurs.
    // Je le laisse vide pour l'instant car les erreurs JSON sont déjà gérées dans les contrôleurs.
    ->withExceptions(function (Exceptions $exceptions): void {
        
    })->create();