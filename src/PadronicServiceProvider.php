<?php

namespace Zucoprince\Padronic;

use Illuminate\Support\ServiceProvider;

class PadronicServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Aqui você pode fazer vinculações no contêiner de serviços, se necessário.
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Commands' => app_path('Console/Commands'),
        ], 'padronic-commands');
    }

}
