<?php

namespace Zucoprince\Padronic\Commands;

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

        $this->info('Comando all foi executado com sucesso!');
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

        file_put_contents($filePath, '');
        File::append($filePath, $apiDefault);

        return $this->info("O arquivo $filePath foi alterado com sucesso!");
    }

    public function incrementRepository($fileName)
    {
        $fileNameLowerVar = '$' . strtolower($fileName);
        $repositoriesPath = base_path('app/Http/Repositories');

        if (!File::isDirectory($repositoriesPath)) {
            File::makeDirectory($repositoriesPath, 0755, true);
        }

        $repositoryFilePath = $repositoriesPath . DIRECTORY_SEPARATOR . $fileName . 'Repository.php';

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            !File::exists($repositoryFilePath) ? exec("echo. > $repositoryFilePath") : $this->error("O arquivo $repositoryFilePath já existe no contexto atual.");
        } else {
            !File::exists($repositoryFilePath) ? exec("touch $repositoryFilePath") : $this->error("O arquivo $repositoryFilePath já existe no contexto atual.");
        }

        $repositoryDefault = $this->repositoryTxt($fileName, $fileNameLowerVar);

        File::append($repositoryFilePath, $repositoryDefault);

        return $this->info("O arquivo $repositoryFilePath criado com sucesso!");
    }

    public function incrementController($fileName)
    {
        $controllerPath = base_path('app/Http/Controllers');
        $controllerFilePath = $controllerPath . DIRECTORY_SEPARATOR . $fileName . 'Controller.php';
        $controllerDefault = $this->controllerTxt($fileName);

        file_put_contents($controllerFilePath, '');
        File::append($controllerFilePath, $controllerDefault);

        return $this->info("O arquivo $controllerFilePath foi alterado com sucesso!");
    }

    public function incrementRoute($fileName)
    {
        $fileNameLower = strtolower($fileName);
        $routesPath = base_path('routes/api');

        if (!File::isDirectory($routesPath)) {
            File::makeDirectory($routesPath, 0755, true);
        }

        $routeFilePath = $routesPath . DIRECTORY_SEPARATOR . $fileName . '.php';

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            !File::exists($routeFilePath) ? exec("echo. > $routeFilePath") : $this->error("O arquivo $routeFilePath já existe no contexto atual.");
        } else {
            !File::exists($routeFilePath) ? exec("touch $routeFilePath") : $this->error("O arquivo $routeFilePath já existe no contexto atual.");
        }

        $routeDefault = $this->routeTxt($fileName, $fileNameLower);

        File::append($routeFilePath, $routeDefault);

        return $this->info("O arquivo $routeFilePath foi criado com sucesso!");
    }

    public function repositoryTxt($fileName, $fileNameLowerVar)
    {
        return "
    <?php

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

    }
    ";
    }

    public function controllerTxt($fileName)
    {
        return "
        <?php

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
        return "
        <?php

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
        });

        ";
    }
}

