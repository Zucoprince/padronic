<?php

namespace Zucoprince\Padronic;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class PadronicServiceProvider extends ServiceProvider
{
    public function register()
    {
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
            $change = $commandsDir . '/' . $file->getFilename();
            $contents = file_get_contents($change);
            $put = str_replace("namespace Zucoprince\Padronic\Commands;", "namespace App\Console\Commands;", $contents);

            file_put_contents($file, $put);
        }
    }

}
