<?php

namespace App\Providers;


use App\Http\Services\BukuService;
use App\Http\Services\PeminjamanService;
use App\Http\Services\StudentService;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BukuService::class);
        $this->app->bind(PeminjamanService::class);
        // $this->app->bind(RegulationService::class);
        $this->app->bind(StudentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
