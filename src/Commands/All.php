<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class All extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:all {names*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando feito para criar todos os arquivos necessários';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileNames = $this->argument('names');

        foreach ($fileNames as $fileName) {
            $this->call('make:model', [
                'name' => $fileName,
                '--migration' => true,
                '--controller' => true,
            ]);

            $this->call('make:resource', [
                'name' => $fileName . 'Resource',
            ]);

            $this->call('make:request', [
                'name' => $fileName . 'Request',
            ]);

            $this->incrementRepository($fileName);
            $this->incrementController($fileName);
            $this->incrementRoute($fileName);
        }

        $this->routeApi();

        $this->info('
         ______________________________________________
        |                                              |
        |                                              |
        |    Comando all foi executado com sucesso!    |
        |                                              |
        |______________________________________________|

'
        );
    }

    public function routeApi()
    {
        $filePath = base_path('routes') . DIRECTORY_SEPARATOR . 'api.php';
        $apiDefault = "
        <?php

        \$dir = __DIR__ . '/api/*';

        foreach (glob(\$dir) as \$file) {
            include \$file;
        }
        ";

        $content = File::get($filePath);
        $especificContent = "$" . "dir = __DIR__ . '/api/*';";

        if (strpos($content, $especificContent) === false) {
            file_put_contents($filePath, '');
            File::append($filePath, $apiDefault);

            return $this->info("O arquivo $filePath foi alterado com sucesso!");
        } else {
            return $this->alert("O arquivo $filePath já se encontra no formato certo");
        }
    }

    public function incrementRepository($fileName)
    {
        $fileNameLowerVar = '$' . strtolower($fileName);
        $repositoriesPath = base_path('app/Http/Repositories');
        $repositoryDefault = $this->repositoryTxt($fileName, $fileNameLowerVar);
        $validResponses = ['S', 'N', 'SIM', 'NAO', 'NÃO'];
        $response = [];

        if (!File::isDirectory($repositoriesPath)) {
            File::makeDirectory($repositoriesPath, 0755, true);
        }

        $repositoryFilePath = $repositoriesPath . DIRECTORY_SEPARATOR . $fileName . 'Repository.php';

        if (!File::exists($repositoryFilePath)) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec("echo. > $repositoryFilePath");
            } else {
                exec("touch $repositoryFilePath");
            }

            File::append($repositoryFilePath, $repositoryDefault);

            return $this->info("O arquivo $repositoryFilePath criado com sucesso!");
        } else {
            echo $this->alert("O arquivo $repositoryFilePath já existe no contexto atual.");

            while (!in_array($response, $validResponses)) {
                $response = "\033[1;33m" . strtoupper($this->ask("Gostaria de reescrever o arquivo $fileName" . "Repository.php para o padrão do Padronic? - [s/n]")) . "\033[0m";
            }

            if (in_array($response, ['S', 'SIM'])) {
                file_put_contents($repositoryFilePath, '');
                File::append($repositoryFilePath, $repositoryDefault);

                return $this->info("O arquivo $repositoryFilePath foi alterado com sucesso!");
            } else {
                return $this->alert("O arquivo $repositoryFilePath não foi alterado");
            }
        }
    }

    public function incrementController($fileName)
    {
        $controllerPath = base_path('app/Http/Controllers');
        $controllerFilePath = $controllerPath . DIRECTORY_SEPARATOR . $fileName . 'Controller.php';
        $controllerDefault = $this->controllerTxt($fileName);
        $content = File::get($controllerFilePath);
        $especificContent = "class $fileName extends Controller
{
    //
}";
        $validResponses = ['S', 'N', 'SIM', 'NAO', 'NÃO'];
        $response = [];

        if (strpos($content, $especificContent) === false) {
            while (!in_array($response, $validResponses)) {
                $response = "\033[1;33m" . strtoupper($this->ask("Gostaria de reescrever o arquivo $fileName" . "Controller.php para o padrão do Padronic? - [s/n]")) . "\033[0m";
            }

            if (in_array($response, ['S', 'SIM'])) {
                file_put_contents($controllerFilePath, '');
                File::append($controllerFilePath, $controllerDefault);

                return $this->info("O arquivo $controllerFilePath foi alterado com sucesso!");
            } else {
                return $this->alert("O arquivo $controllerFilePath não foi alterado");
            }
        } else {
            file_put_contents($controllerFilePath, '');
            File::append($controllerFilePath, $controllerDefault);

            return $this->info("O arquivo $controllerFilePath foi alterado com sucesso!");
        }
    }

    public function incrementRoute($fileName)
    {
        $fileNameLower = strtolower($fileName);
        $routesPath = base_path('routes/api');
        $routeDefault = $this->routeTxt($fileName, $fileNameLower);
        $validResponses = ['S', 'N', 'SIM', 'NAO', 'NÃO'];
        $response = [];

        if (!File::isDirectory($routesPath)) {
            File::makeDirectory($routesPath, 0755, true);
        }

        $routeFilePath = $routesPath . DIRECTORY_SEPARATOR . $fileName . '.php';

        if (!File::exists($routeFilePath)) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec("echo. > $routeFilePath");
            } else {
                exec("touch $routeFilePath");
            }

            File::append($routeFilePath, $routeDefault);

            return $this->info("O arquivo $routeFilePath foi criado com sucesso!");
        } else {
            echo $this->alert("O arquivo $routeFilePath já existe no contexto atual.");

            while (!in_array($response, $validResponses)) {
                $response = "\033[1;33m" . strtoupper($this->ask("Gostaria de reescrever o arquivo $fileName" . ".php (Routes) para o padrão do Padronic? - [s/n]")) . "\033[0m";
            }

            if (in_array($response, ['S', 'SIM'])) {
                file_put_contents($routeFilePath, '');
                File::append($routeFilePath, $routeDefault);

                return $this->info("O arquivo $routeFilePath foi alterado com sucesso!");
            } else {
                return $this->alert("O arquivo $routeFilePath não foi alterado");
            }
        }
    }

    public function repositoryTxt($fileName, $fileNameLowerVar)
    {
        return "<?php

namespace App\Http\Repositories;

use App\Models\\$fileName;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class {$fileName}Repository
{
    protected \$model;

    public function __construct($fileName \$model)
    {
        \$this->model = \$model;
    }

    public function query(\$model, \$request): $fileName
    {
        foreach (\$request as \$key => \$value) {
            switch (\$key) {
                // Faça todos os querys necessarios aqui.
                default:
                    break;
            }
        }

        return \$model;
    }

    public function getAll(): LengthAwarePaginator
    {
        $fileNameLowerVar = \$this->model->orderBy('id');
        \$query = \$this->query($fileNameLowerVar, request()->all());

        return \$query->paginate(10);
    }

    public function getById(int \$id): $fileName
    {
        $fileNameLowerVar = \$this->model->findOrFail(\$id);

        return $fileNameLowerVar;
    }

    public function create(array \$data): $fileName|Exception
    {
        try {
            DB::beginTransaction();

            $fileNameLowerVar = \$this->model->create(\$data);

            DB::commit();

            return $fileNameLowerVar;
        } catch (Exception \$e) {
            DB::rollBack();
            Log::error(\$e->getMessage());

            throw \$e;
        }
    }

    public function update(int \$id, array \$data): $fileName|Exception
    {
        try {
            DB::beginTransaction();

            $fileNameLowerVar = \$this->model->findOrFail(\$id);

            {$fileNameLowerVar}->fill(\$data);
            {$fileNameLowerVar}->save();

            DB::commit();

            return $fileNameLowerVar;
        } catch (Exception \$e) {
            DB::rollBack();
            Log::error(\$e->getMessage());

            throw \$e;
        }
    }

    public function delete(int \$id): String|Exception
    {
        try {
            DB::beginTransaction();

            $fileNameLowerVar = \$this->model->findOrFail(\$id);
            \$id = {$fileNameLowerVar}->id;

            {$fileNameLowerVar}->delete();

            DB::commit();

            return 'O $fileName ' . \$id . ' foi excluído com sucesso!';
        } catch (Exception \$e) {
            DB::rollBack();
            Log::error(\$e->getMessage());

            throw \$e;
        }
    }

}";
    }

    public function controllerTxt($fileName)
    {
        return "<?php

namespace App\Http\Controllers;

use App\Http\Repositories\\{$fileName}Repository;
use App\Http\Requests\\{$fileName}Request;
use App\Http\Resources\\{$fileName}Resource;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class {$fileName}Controller extends Controller
{
    use ApiResponser;

    protected \$repository;

    public function __construct({$fileName}Repository \$repository)
    {
        \$this->repository = \$repository;
    }

    public function callGetAll(): JsonResponse
    {
        \$data = \$this->repository->getAll();
        empty(\$data) || \$data === null ?
            \$response = \$this->errorResponse('Não foi possível trazer nada de {$fileName}, está vazio ou nulo.', Response::HTTP_EXPECTATION_FAILED) :
            \$response = \$this->successResponse({$fileName}Resource::collection(\$data)->response()->getData());

        return \$response;
    }

    public function callCreate({$fileName}Request \$request): JsonResponse
    {
        \$data = \$this->repository->create(\$request->all());

        empty(\$data) || \$data === null ?
            \$response = \$this->errorResponse('Não foi possível criar uma instância de {$fileName}, está vazio ou nulo.', Response::HTTP_EXPECTATION_FAILED) :
            \$response = \$this->successResponse(new {$fileName}Resource(\$data));

        return \$response;
    }

    public function callGetById(int \$id): JsonResponse
    {
        \$data = \$this->repository->getById(\$id);

        empty(\$data) || \$data === null ?
            \$response = \$this->errorResponse('Não foi possível trazer nada de {$fileName}, está vazio ou nulo.', Response::HTTP_EXPECTATION_FAILED) :
            \$response = \$this->successResponse(new {$fileName}Resource(\$data));

        return \$response;
    }

    public function callUpdate(int \$id, {$fileName}Request \$request): JsonResponse
    {
        \$data = \$this->repository->update(\$id, \$request->all());

        empty(\$data) || \$data === null ?
            \$response = \$this->errorResponse('Não foi possível trazer nada de {$fileName}, está vazio ou nulo.', Response::HTTP_EXPECTATION_FAILED) :
            \$response = \$this->successResponse(new {$fileName}Resource(\$data));

        return \$response;
    }

    public function callDelete(int \$id): JsonResponse
    {
        \$data = \$this->repository->delete(\$id);

        empty(\$data) || \$data === null ?
            \$response = \$this->errorResponse('Não foi possível deletar {$fileName}, está vazio ou nulo.', Response::HTTP_EXPECTATION_FAILED) :
            \$response = \$this->successResponse(\$data);

        return \$response;
    }
}

        ";
    }

    public function routeTxt($fileName, $fileNameLower)
    {
        return "<?php

use App\Http\Controllers\\{$fileName}Controller;
use Illuminate\Support\Facades\Route;

Route::prefix('$fileNameLower')
->name('$fileNameLower.')
->group(function () {
    Route::get('/', [{$fileName}Controller::class, 'callGetAll'])->name('get.all');
    Route::post('/', [{$fileName}Controller::class, 'callCreate'])->name('create');
    Route::get('/{ID}', [{$fileName}Controller::class, 'callGetById'])->name('get.by.id');
    Route::put('/{ID}', [{$fileName}Controller::class, 'callUpdate'])->name('update');
    Route::delete('/{ID}', [{$fileName}Controller::class, 'callDelete'])->name('delete');
});";

    }
}

