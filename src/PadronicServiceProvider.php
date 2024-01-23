<?php

namespace Zucoprince\Padronic;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class PadronicServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->addProviderToConfig();
        $this->addCommandsToCommands();
    }

    public function boot()
    {
        //
    }

    protected function addCommandsToCommands()
    {
        $commandsDir = base_path('app/Console/Commands');

        if (!File::isDirectory($commandsDir)) {
            File::makeDirectory($commandsDir, 0755, true);
        }

        $files = File::allFiles(__DIR__ . '/Commands');

        foreach ($files as $file) {
            File::copy($file->getPathname(), $commandsDir . '/' . $file->getFilename());
        }
    }

    protected function addProviderToConfig()
    {
        $file = base_path('config/app.php');
        $contents = file_get_contents($file);
        $provider = 'Zucoprince\Padronic\PadronicServiceProvider::class,';

        if (strpos($contents, $provider) === false) {
            $put = str_replace("'providers' => ServiceProvider::defaultProviders()->merge([", "'providers' => ServiceProvider::defaultProviders()->merge([\n        $provider", $contents);

            file_put_contents($file, $put);
        }
    }

}
