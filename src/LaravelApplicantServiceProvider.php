<?php

namespace Te7aHoudini\LaravelApplicant;

use Illuminate\Support\ServiceProvider;

class LaravelApplicantServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/laravel-applicant.php' => config_path('laravel-applicant.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../database/migrations/create_applications_table.php.stub' => $this->app->databasePath()."/migrations/".date('Y_m_d_His')."_create_applications_table.php",
            ], 'migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-applicant.php', 'laravel-applicant');
    }
}
