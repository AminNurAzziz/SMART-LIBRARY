<?php

namespace App\Providers;


use App\Http\Services\BookService;
use App\Http\Services\BorrowingService;
use App\Http\Services\StudentService;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BookService::class);
        $this->app->bind(BorrowingService::class);
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
