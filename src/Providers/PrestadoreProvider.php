<?php

namespace Prestadores\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class PrestadoreProvider extends ServiceProvider
{
    public function boot(): void
    {


        Vite::prefetch(concurrency: 3);

        \Log::info(date("d-m-Y H:i:s") . " Modulo de Prestadores de serviços");
        Route::middleware('web')->group(__DIR__ . '/../routes/web.php');
    }
}