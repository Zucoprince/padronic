<?php

namespace Zucoprince\Padronic;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class PadronicServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$all = File::exists(base_path('app/Console/Commands/All.php'));
		$rmAll = File::exists(base_path('app/Console/Commands/RmAll.php'));
		$apiResponser = File::exists(base_path('app/Traits/ApiResponser.php'));
		$codeResponser = File::exists(base_path('app/Traits/CodeResponser.php'));

		if (!$all || !$rmAll) {
			$this->addCommandsToCommands();
		}
		if (!$apiResponser) {
			$this->addApiResponserTrait();
		}
		if (!$codeResponser) {
			$this->addCodeResponserTrait();
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
		$apiResponserContent = $this->apiResponserTxt();

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

	protected function addCodeResponserTrait()
	{
		$traitsDir = base_path('app/Traits');
		$codeResponserFilePath = $traitsDir . DIRECTORY_SEPARATOR . 'CodeResponser.php';
		$codeResponserContent = $this->codeResponserTxt();

		if (!File::isDirectory($traitsDir)) {
			File::makeDirectory($traitsDir, 0755, true);
		}

		if (!File::exists($codeResponserFilePath)) {
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				exec("echo. > $codeResponserFilePath");
				echo "O arquivo $codeResponserFilePath foi criado com sucesso!";
			} else {
				exec("touch $codeResponserFilePath");
				echo "O arquivo $codeResponserFilePath foi criado com sucesso!";
			}

			File::append($codeResponserFilePath, $codeResponserContent);

			echo "O arquivo $codeResponserFilePath foi modificado com sucesso!";
		} else {
			echo "O arquivo $codeResponserFilePath já existe no contexto atual.";
		}
	}

	protected function apiResponserTxt()
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

	protected function codeResponserTxt()
	{
		return "<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait CodeResponser
{
    use ApiResponser;

	protected \$successCodes = [
    	200 => Response::HTTP_OK,
    	201 => Response::HTTP_CREATED,
    	202 => Response::HTTP_ACCEPTED,
    	203 => Response::HTTP_NON_AUTHORITATIVE_INFORMATION,
    	204 => Response::HTTP_NO_CONTENT,
    	205 => Response::HTTP_RESET_CONTENT,
    	206 => Response::HTTP_PARTIAL_CONTENT,
    	207 => Response::HTTP_MULTI_STATUS,
    	226 => Response::HTTP_IM_USED,
	];

	protected \$errorCodes = [
    	400 => Response::HTTP_BAD_REQUEST,
    	401 => Response::HTTP_UNAUTHORIZED,
    	402 => Response::HTTP_PAYMENT_REQUIRED,
    	403 => Response::HTTP_FORBIDDEN,
    	404 => Response::HTTP_NOT_FOUND,
    	405 => Response::HTTP_METHOD_NOT_ALLOWED,
    	406 => Response::HTTP_NOT_ACCEPTABLE,
    	407 => Response::HTTP_PROXY_AUTHENTICATION_REQUIRED,
    	408 => Response::HTTP_REQUEST_TIMEOUT,
    	409 => Response::HTTP_CONFLICT,
    	410 => Response::HTTP_GONE,
    	411 => Response::HTTP_LENGTH_REQUIRED,
    	412 => Response::HTTP_PRECONDITION_FAILED,
    	413 => Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
    	414 => Response::HTTP_REQUEST_URI_TOO_LONG,
    	415 => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
    	416 => Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE,
    	417 => Response::HTTP_EXPECTATION_FAILED,
    	418 => Response::HTTP_IM_A_TEAPOT, // Este é um código de status sarcástico.
    	422 => Response::HTTP_UNPROCESSABLE_ENTITY,
    	423 => Response::HTTP_LOCKED,
    	424 => Response::HTTP_FAILED_DEPENDENCY,
    	426 => Response::HTTP_UPGRADE_REQUIRED,
    	428 => Response::HTTP_PRECONDITION_REQUIRED,
   		429 => Response::HTTP_TOO_MANY_REQUESTS,
   	 	431 => Response::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE,
   	 	451 => Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS,
    	500 => Response::HTTP_INTERNAL_SERVER_ERROR,
    	501 => Response::HTTP_NOT_IMPLEMENTED,
    	502 => Response::HTTP_BAD_GATEWAY,
    	503 => Response::HTTP_SERVICE_UNAVAILABLE,
    	504 => Response::HTTP_GATEWAY_TIMEOUT,
    	505 => Response::HTTP_HTTP_VERSION_NOT_SUPPORTED,
    	511 => Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED,
	];

	protected \$messages = [
    	// Códigos de sucesso
    	200 => 'Operação realizada com sucesso.',
    	201 => 'Recurso criado com sucesso.',
    	202 => 'A solicitação foi aceita para processamento, mas o processamento não foi concluído.',
    	203 => 'Informação não autoritativa. O servidor está retornando informações que podem não ser as mais recentes.',
    	204 => 'Sem conteúdo. A solicitação foi bem-sucedida, mas não há conteúdo para retornar.',
    	205 => 'Conteúdo redefinido. O conteúdo foi redefinido com sucesso.',
    	206 => 'Conteúdo parcial. A solicitação foi parcialmente atendida.',
    	207 => 'Multi-status. A resposta contém múltiplos status.',
    	226 => 'IM usado. O servidor cumpriu a solicitação e a resposta contém informações sobre o uso do recurso.',
    
    	// Códigos de erro
   		400 => 'A solicitação é inválida.',
    	401 => 'Credenciais inválidas ou ausentes.',
    	402 => 'Pagamento necessário. A solicitação requer pagamento.',
    	403 => 'Acesso proibido. Você não tem permissão para acessar este recurso.',
    	404 => 'Recurso não encontrado. O que você está procurando pode ter sido removido ou nunca existiu.',
    	405 => 'Método não permitido. O método especificado na solicitação não é permitido para o recurso solicitado.',
    	406 => 'Não aceitável. O recurso solicitado é capaz de gerar apenas conteúdo que não é aceito pelo agente do usuário.',
    	407 => 'Autenticação proxy necessária. Você deve se autenticar com o proxy.',
    	408 => 'Tempo limite da solicitação. O servidor não recebeu uma solicitação completa no tempo permitido.',
    	409 => 'Conflito. A solicitação não pôde ser concluída devido a um conflito com o estado atual do recurso.',
    	410 => 'Recurso indisponível. O recurso solicitado não está mais disponível.',
    	411 => 'Comprimento necessário. O servidor não aceita solicitações sem um cabeçalho de comprimento.',
    	412 => 'Falha de pré-condição. Uma das pré-condições da solicitação foi avaliada como falsa.',
    	413 => 'Entidade da solicitação muito grande. O servidor não pode processar a solicitação devido ao tamanho excessivo.',
    	414 => 'URI da solicitação muito longa. O URI solicitado é maior do que o que o servidor pode processar.',
    	415 => 'Tipo de mídia não suportado. O formato da mídia da solicitação é inválido.',
    	416 => 'Faixa solicitada não satisfatória. O servidor não pode fornecer a parte do recurso solicitada.',
    	417 => 'Expectativa falhada. O servidor não pode atender à expectativa do cabeçalho da solicitação.',
    	418 => 'Estou teapot. Este é um código de status sarcástico indicando que o servidor é um bule e não pode preparar café.',
    	422 => 'Entidade não processável. A solicitação estava bem formada, mas não pôde ser seguida devido a erros de validação.',
    	423 => 'Bloqueado. O recurso solicitado está bloqueado e não pode ser acessado.',
    	424 => 'Falha de dependência. A solicitação falhou porque uma pré-condição falhou.',
    	426 => 'Upgrade necessário. O cliente deve mudar para um protocolo diferente.',
    	428 => 'Pré-condição necessária. O servidor requer que a solicitação seja condicional.',
    	429 => 'Muitos pedidos. O usuário enviou muitas solicitações em um determinado período.',
    	431 => 'Campos de cabeçalho da solicitação muito grandes. O servidor não pode processar os campos de cabeçalho.',
    	451 => 'Indisponível por razões legais. O recurso solicitado não está disponível devido a restrições legais.',
    	500 => 'Erro interno no servidor. Ocorreu um erro inesperado ao processar sua solicitação.',
    	501 => 'Não implementado. O servidor não suporta a funcionalidade necessária para atender à solicitação.',
    	502 => 'Bad Gateway. O servidor recebeu uma resposta inválida ao tentar processar a solicitação.',
    	503 => 'Serviço indisponível. O servidor não está disponível no momento, geralmente devido a manutenção.',
    	504 => 'Gateway Timeout. O servidor não recebeu uma resposta a tempo de outro servidor ao processar sua solicitação.',
    	505 => 'Versão HTTP não suportada. O servidor não suporta a versão do protocolo HTTP usado na solicitação.',
    	511 => 'Autenticação de rede necessária. O cliente deve se autenticar para obter acesso à rede.',
	];

    public function codeResponse(\$code = 200, \$data = null)
    {
        if (array_key_exists(\$code, \$this->successCodes)) {
            return \$this->successResponse(\$data ?? \$this->messages[\$code], \$this->successCodes[\$code]);
        }

        if (array_key_exists(\$code, \$this->errorCodes)) {
            return \$this->errorResponse(\$this->messages[\$code], \$this->errorCodes[\$code]);
        }

        // Código padrão para status desconhecido
        return \$this->errorResponse('Código de status desconhecido.', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}";
	}
}
