<?php

namespace Zucoprince\Padronic;

use Illuminate\Support\ServiceProvider;

class PadronicSetupServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Adiciona o provedor de serviços ao arquivo config/app.php
        $this->addProviderToConfig();
    }

    protected function addProviderToConfig()
    {
        $configPath = config_path('app.php');
        $configContents = file_get_contents($configPath);

        // Verifica se o provedor já está registrado no arquivo
        if (strpos($configContents, 'Zucoprince\Padronic\PadronicServiceProvider::class') === false) {
            // Adiciona o provedor ao array de provedores
            $replacement = "        Zucoprince\Padronic\PadronicServiceProvider::class,";

            // Encontra a linha onde os provedores são definidos
            $pattern = '/\'providers\'\s*=>\s*\[([^\]]*)\]/s';
            preg_match($pattern, $configContents, $matches);

            // Insere o novo provedor na linha encontrada
            $newConfig = str_replace($matches[0], preg_replace('/(\s*)\]/', "$1$replacement\n$1]", $matches[0]), $configContents);

            // Salva as alterações de volta ao arquivo
            file_put_contents($configPath, $newConfig);
        }
    }
}
