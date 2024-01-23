<?php

namespace Zucoprince\Padronic;

use Illuminate\Support\ServiceProvider;

class PadronicServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->addProviderToConfig();

        $this->publishes([
            __DIR__ . '/Commands' => app_path('Console/Commands'),
        ], 'padronic-commands');
    }

    protected function addProviderToConfig()
    {
        $file = base_path('config/app.php');
        $contents = file_get_contents($file);
        $provider = 'Zucoprince\Padronic\PadronicServiceProvider::class,';

        $put = str_replace("'providers' => ServiceProvider::defaultProviders()->merge([", "'providers' => ServiceProvider::defaultProviders()->merge([\n        $provider", $contents);

        file_put_contents($file, $put);
    }

}
