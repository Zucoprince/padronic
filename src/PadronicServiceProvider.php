<?php

namespace Zucoprince\Padronic;

use Illuminate\Support\ServiceProvider;

class PadronicServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(PadronicSetupServiceProvider::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Commands' => app_path('Console/Commands'),
        ], 'padronic-commands');
    }

}
