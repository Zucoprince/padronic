<?php

namespace Zucoprince\Padronic;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RmAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rmall {names*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para remover todos os arquivos relacionados a ele';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileNames = $this->argument('names');

        foreach ($fileNames as $fileName) {
            $repositoriesPath = base_path('app/Http/Repositories/' . $fileName . 'Repository.php');
            $routesPath = base_path('routes/api/' . $fileName . '.php');
            $modelPath = base_path('app/Models/' . $fileName . '.php');
            $controllerPath = base_path('app/Http/Controllers/' . $fileName . 'Controller.php');
            $resourcePath = base_path('app/Http/Resources/' . $fileName . 'Resource.php');
            $requestPath = base_path('app/Http/Requests/' . $fileName . 'Request.php');
            $migrationPath = base_path('database/migrations');

            // Obtenha todas as migrações no diretório
            $migrations = File::glob("{$migrationPath}/*.php");

            // Percorra as migrações para encontrar a correspondente ao nome
            foreach ($migrations as $migration) {
                preg_match('/create_(\w+)_table/', $migration, $matches);

                if (ucfirst($matches[1]) === $fileName . 's') {
                    // Encontrou a migration
                    $this->info("Encontrou a migration: {$migration}");

                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        // Windows
                        exec("del /s /q $migration");
                    } else {
                        // Outros sistemas operacionais
                        exec("rm -rf $migration");
                    }

                    break;
                }
            }

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows
                exec("del /s /q $repositoriesPath");
                $this->info("Encontrou o repository: $repositoriesPath");
                exec("del /s /q $routesPath");
                $this->info("Encontrou a rota: $routesPath");
                exec("del /s /q $modelPath");
                $this->info("Encontrou o model: $modelPath");
                exec("del /s /q $controllerPath");
                $this->info("Encontrou o controller: $controllerPath");
                exec("del /s /q $resourcePath");
                $this->info("Encontrou o resource: $resourcePath");
                exec("del /s /q $requestPath");
                $this->info("Encontrou o request: $requestPath");
            } else {
                // Outros sistemas operacionais
                exec("rm -rf $repositoriesPath");
                $this->info("Encontrou o repository: $repositoriesPath");
                exec("rm -rf $routesPath");
                $this->info("Encontrou a rota: $routesPath");
                exec("rm -rf $modelPath");
                $this->info("Encontrou o model: $modelPath");
                exec("rm -rf $controllerPath");
                $this->info("Encontrou o controller: $controllerPath");
                exec("rm -rf $resourcePath");
                $this->info("Encontrou o resource: $resourcePath");
                exec("rm -rf $requestPath");
                $this->info("Encontrou o request: $requestPath");
            }
        }

        $this->info("Todos os arquivos foram excluídos com sucesso!");
    }
}
