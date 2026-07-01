<?php

namespace App\Providers;

use App\Services\AIService;
use App\Services\GeminiAIService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the AIService interface to the Gemini implementation
        $this->app->bind(AIService::class, GeminiAIService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
