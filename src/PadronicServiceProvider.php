<?php

namespace Zucoprince\Padronic;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class PadronicServiceProvider extends ServiceProvider
{
    protected $all;
    protected $rmAll;
    protected $apiResponser;

    public function __construct()
    {
        $this->all = File::exists(base_path('app/Console/Commands/All.php'));
        $this->rmAll = File::exists(base_path('app/Console/Commands/RmAll.php'));
        $this->apiResponser = File::exists(base_path('app/Traits/ApiResponser.php'));
    }

    public function boot()
    {
        if (!$this->all || !$this->rmAll) {
            $this->addCommandsToCommands();
        }
        if (!$this->apiResponser) {
            $this->addApiResponserTrait();
        }
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

    protected function addApiResponserTrait()
    {
        $traitsDir = base_path('app/Traits');
        $apiResponserFilePath = $traitsDir . DIRECTORY_SEPARATOR . 'ApiResponser.php';
        $apiResponserContent = $this->apiReponserTxt();

        if (!File::isDirectory($traitsDir)) {
            File::makeDirectory($traitsDir, 0755, true);
        }

        if (!File::exists($apiResponserFilePath)) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec("echo. > $apiResponserFilePath");
                echo "O arquivo $apiResponserFilePath foi criado com sucesso!";
            } else {
                exec("touch $apiResponserFilePath");
                echo "O arquivo $apiResponserFilePath foi criado com sucesso!";
            }

            File::append($apiResponserFilePath, $apiResponserContent);

            echo "O arquivo $apiResponserFilePath foi modificado com sucesso!";
        } else {
            echo "O arquivo $apiResponserFilePath já existe no contexto atual.";
        }
    }

    protected function apiReponserTxt()
    {
        return "<?php

namespace App\Traits;
        
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponser
{
    // Resposta de sucesso para a API.

    public function successResponse(\$data = null, \$code = Response::HTTP_OK): JsonResponse
    {
        return response()->json(
            \$data,
            \$code
        )->header('Content-Type', 'application/json');
    }


    // * Resposta de erro para a API.

    public function errorResponse(\$message, \$code): JsonResponse
    {
        return response()->json(([
            'message' => \$message,
            'code' => \$code
        ]), \$code);
    }
}";
    }

}