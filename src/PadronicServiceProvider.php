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

    public function codeResponse(\$code = 200, \$data = null)
    {
        switch (\$code) {
            case 100:
                \$response = \$this->successResponse('Ocorreu um erro inesperado ao processar a sua solicitação. Por favor, tente novamente mais tarde ou entre em contato com o suporte técnico para obter assistência.', Response::HTTP_CONTINUE);
                break;
            case 101:
                \$response = \$this->successResponse('O servidor está mudando os protocolos conforme solicitado. Aguarde enquanto a comunicação é estabelecida usando o novo protocolo.', Response::HTTP_SWITCHING_PROTOCOLS);
                break;
            case 102:
                \$response = \$this->successResponse('O servidor está processando a solicitação. Por favor, aguarde enquanto o processamento é concluído.', Response::HTTP_PROCESSING);
                break;
            case 103:
                \$response = \$this->successResponse('O servidor está enviando informações preliminares antes da resposta completa. Aguarde enquanto mais dados são enviados.', Response::HTTP_EARLY_HINTS);
                break;
            case 200:
                \$response = \$this->successResponse(\$data, Response::HTTP_OK);
                break;
            case 201:
                \$response = \$this->successResponse(\$data, Response::HTTP_CREATED);
                break;
            case 202:
                \$response = \$this->successResponse(\$data, Response::HTTP_ACCEPTED);
                break;
            case 203:
                \$response = \$this->successResponse(\$data, Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
                break;
            case 204:
                \$response = \$this->successResponse(\$data, Response::HTTP_NO_CONTENT);
                break;
            case 205:
                \$response = \$this->successResponse(null, Response::HTTP_RESET_CONTENT);
                break;
            case 206:
                \$response = \$this->successResponse(\$data, Response::HTTP_PARTIAL_CONTENT);
                break;
            case 207:
                \$response = \$this->successResponse(\$data, Response::HTTP_MULTI_STATUS);
                break;
            case 226:
                \$response = \$this->successResponse(\$data, Response::HTTP_IM_USED);
                break;
            case 300:
                \$response = \$this->successResponse(null, Response::HTTP_MULTIPLE_CHOICES);
                break;
            case 301:
                \$response = \$this->successResponse(\$data, Response::HTTP_MOVED_PERMANENTLY);
                break;
            case 302:
                \$response = \$this->successResponse(\$data, Response::HTTP_FOUND);
                break;
            case 303:
                \$response = \$this->successResponse(\$data, Response::HTTP_SEE_OTHER);
                break;
            case 304:
                \$response = \$this->successResponse(null, Response::HTTP_NOT_MODIFIED);
                break;
            case 307:
                \$response = \$this->successResponse(\$data, Response::HTTP_TEMPORARY_REDIRECT);
                break;
            case 308:
                \$response = \$this->successResponse(\$data, Response::HTTP_PERMANENTLY_REDIRECT);
                break;
            case 400:
                \$response = \$this->errorResponse('A solicitação do cliente não pôde ser entendida pelo servidor devido a uma sintaxe incorreta ou malformada. Verifique se todos os parâmetros e cabeçalhos da solicitação estão corretos e tente novamente.', Response::HTTP_BAD_REQUEST);
                break;
            case 401:
                \$response = \$this->errorResponse('Não autorizado: o acesso à solicitação é negado devido à falta de credenciais válidas ou à autenticação incorreta. Por favor, faça login com credenciais válidas e tente novamente.', Response::HTTP_UNAUTHORIZED);
                break;
            case 402:
                \$response = \$this->errorResponse('Pagamento necessário: o acesso à solicitação requer o pagamento de uma taxa ou subscrição. Por favor, realize o pagamento necessário e tente novamente.', Response::HTTP_PAYMENT_REQUIRED);
                break;
            case 403:
                \$response = \$this->errorResponse('Acesso proibido: você não tem permissão para acessar esta solicitação devido a restrições de acesso. Entre em contato com o administrador do sistema para obter assistência.', Response::HTTP_FORBIDDEN);
                break;
            case 404:
                \$response = \$this->errorResponse('Recurso não encontrado: o servidor não conseguiu encontrar o recurso solicitado. Verifique o URL e tente novamente. Se o problema persistir, entre em contato com o administrador do sistema para obter assistência.', Response::HTTP_NOT_FOUND);
                break;
            case 405:
                \$response = \$this->errorResponse('Método não permitido: o método de solicitação utilizado não é permitido para este recurso. Verifique o método de solicitação e tente novamente. Se o problema persistir, entre em contato com o administrador do sistema para obter assistência.', Response::HTTP_METHOD_NOT_ALLOWED);
                break;
            case 406:
                \$response = \$this->errorResponse('Mídia não aceitável: o servidor não é capaz de fornecer uma resposta que atenda aos critérios de aceitação fornecidos pela solicitação. Verifique os cabeçalhos 'Accept' da solicitação e tente novamente.', Response::HTTP_NOT_ACCEPTABLE);
                break;
            case 407:
                \$response = \$this->errorResponse('Autenticação de proxy necessária: é necessária autenticação para acessar este recurso através do proxy. Forneça as credenciais de autenticação necessárias e tente novamente.', Response::HTTP_PROXY_AUTHENTICATION_REQUIRED);
                break;
            case 408:
                \$response = \$this->errorResponse('Tempo limite de solicitação: o servidor encerrou a conexão devido a um tempo limite de solicitação. Verifique sua conexão com a internet e tente novamente.', Response::HTTP_REQUEST_TIMEOUT);
                break;
            case 409:
                \$response = \$this->errorResponse('Conflito: a solicitação não pôde ser concluída devido a um conflito com o estado atual do recurso. Verifique a solicitação e tente novamente.', Response::HTTP_CONFLICT);
                break;
            case 410:
                \$response = \$this->errorResponse('Recurso não disponível: o recurso solicitado não está mais disponível no servidor e não há redirecionamento disponível. Atualize seus links e tente encontrar uma versão atualizada do recurso.', Response::HTTP_GONE);
                break;
            case 411:
                \$response = \$this->errorResponse('Comprimento necessário: o servidor requer que o cabeçalho 'Content-Length' esteja presente na solicitação. Forneça o comprimento do conteúdo e tente novamente.', Response::HTTP_LENGTH_REQUIRED);
                break;
            case 412:
                \$response = \$this->errorResponse('Falha na condição prévia: uma ou mais condições definidas na solicitação falharam no servidor. Verifique as condições prévias e tente novamente.', Response::HTTP_PRECONDITION_FAILED);
                break;
            case 413:
                \$response = \$this->errorResponse('Carga útil muito grande: a solicitação excede o limite de tamanho permitido pelo servidor. Reduza o tamanho da solicitação e tente novamente.', Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
                break;
            case 414:
                \$response = \$this->errorResponse('URI muito longo: o URI solicitado é muito longo para ser processado pelo servidor. Reduza o comprimento do URI e tente novamente.', Response::HTTP_REQUEST_URI_TOO_LONG);
                break;
            case 415:
                \$response = \$this->errorResponse('Tipo de mídia não suportado: o servidor não suporta o tipo de mídia da solicitação. Verifique o tipo de mídia e tente novamente com um tipo suportado.', Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
                break;
            case 416:
                \$response = \$this->errorResponse('Intervalo não satisfatório: o servidor não pode satisfazer o intervalo de bytes solicitado na solicitação. Verifique o intervalo especificado e tente novamente.', Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE);
                break;
            case 417:
                \$response = \$this->errorResponse('Expectativa não atendida: o servidor não pode atender às expectativas indicadas no cabeçalho 'Expect' da solicitação. Verifique as expectativas e tente novamente.', Response::HTTP_EXPECTATION_FAILED);
                break;
            case 418:
                // Como mencionado anteriormente, este é um código de status sarcástico e geralmente não é usado em situações reais.
                break;
            case 419:
                \$response = \$this->errorResponse('Tempo limite de autenticação: o servidor encerrou a sessão devido a um tempo limite de autenticação. Faça login novamente e tente novamente.', Response::HTTP_AUTHENTICATION_TIMEOUT);
                break;
            case 420:
                // Como mencionado anteriormente, este é um código de status experimental e geralmente não é usado em situações reais.
                break;
            case 421:
                \$response = \$this->errorResponse('Requisição mal direcionada: a requisição foi direcionada a um servidor inapto a produzir a resposta. Pode ser enviado por um servidor que não está configurado para produzir respostas para a combinação de esquema e autoridade inclusas na URI da requisição.', Response::HTTP_MISDIRECTED_REQUEST);
                break;
            case 422:
                \$response = \$this->errorResponse('Conteúdo inválido: a solicitação foi bem formada, mas não pôde ser atendida devido a erros semânticos.', Response::HTTP_UNPROCESSABLE_ENTITY);
                break;
            case 423:
                \$response = \$this->errorResponse('Recurso bloqueado: o recurso que está sendo acessado está bloqueado.', Response::HTTP_LOCKED);
                break;
            case 424:
                \$response = \$this->errorResponse('Dependência falhou: a solicitação falhou devido à falha de uma solicitação anterior.', Response::HTTP_FAILED_DEPENDENCY);
                break;
            case 425:
                // Este é um código de status experimental e geralmente não é usado em situações reais.
                break;
            case 426:
                \$response = \$this->errorResponse('Atualização necessária: o servidor se recusa a executar a solicitação usando o protocolo atual, mas pode estar disposto a fazê-lo depois que o cliente atualizar para um protocolo diferente.', Response::HTTP_UPGRADE_REQUIRED);
                break;
            case 428:
                \$response = \$this->errorResponse('Pré-condição necessária: o servidor de origem exige que a solicitação seja condicional para evitar o problema de 'atualização perdida'.', Response::HTTP_PRECONDITION_REQUIRED);
                break;
            case 429:
                \$response = \$this->errorResponse('Muitas requisições: o usuário enviou muitas requisições num dado tempo ('limitação de frequência').', Response::HTTP_TOO_MANY_REQUESTS);
                break;
            case 431:
                \$response = \$this->errorResponse('Campos de cabeçalho da requisição muito grandes: o servidor não está disposto a processar a solicitação porque seus campos de cabeçalho são muito grandes.', Response::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE);
                break;
            case 451:
                \$response = \$this->errorResponse('Indisponível por motivos legais: o agente do usuário solicitou um recurso que não pode ser fornecido legalmente, como uma página da Web censurada por um governo.', Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS);
                break;
            case 500:
                \$response = \$this->errorResponse('Erro interno do servidor: o servidor encontrou uma situação com a qual não sabe lidar.', Response::HTTP_INTERNAL_SERVER_ERROR);
                break;
            case 501:
                \$response = \$this->errorResponse('Não implementado: o método da requisição não é suportado pelo servidor e não pode ser manipulado.', Response::HTTP_NOT_IMPLEMENTED);
                break;
            case 502:
                \$response = \$this->errorResponse('Gateway ruim: o servidor, enquanto trabalhava como um gateway para obter uma resposta necessária para lidar com a solicitação, obteve uma resposta inválida.', Response::HTTP_BAD_GATEWAY);
                break;
            case 503:
                \$response = \$this->errorResponse('Serviço indisponível: o servidor não está pronto para manipular a requisição. Causas comuns são um servidor em manutenção ou sobrecarregado.', Response::HTTP_SERVICE_UNAVAILABLE);
                break;
            case 504:
                \$response = \$this->errorResponse('Tempo limite do gateway: o servidor atuando como um gateway não conseguiu obter uma resposta a tempo.', Response::HTTP_GATEWAY_TIMEOUT);
                break;
            case 505:
                \$response = \$this->errorResponse('Versão HTTP não suportada: a versão HTTP usada na requisição não é suportada pelo servidor.', Response::HTTP_VERSION_NOT_SUPPORTED);
                break;
            case 506:
                // Este é um código de status raramente utilizado e específico para casos de negociação de conteúdo transparente.
                break;
            case 507:
                \$response = \$this->errorResponse('Armazenamento insuficiente: o servidor não pode armazenar a representação necessária para concluir a solicitação com êxito.', Response::HTTP_INSUFFICIENT_STORAGE);
                break;
            case 508:
                \$response = \$this->errorResponse('Loop detectado: o servidor detectou um loop infinito ao processar a solicitação.', Response::HTTP_LOOP_DETECTED);
                break;
            case 510:
                \$response = \$this->errorResponse('Não estendido: extensões adicionais à solicitação são necessárias para que o servidor a atenda.', Response::HTTP_NOT_EXTENDED);
                break;
            case 511:
                \$response = \$this->errorResponse('Autenticação de rede necessária: indica que o cliente precisa se autenticar para obter acesso à rede.', Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED);
                break;
            default:
                \$response = \$this->successResponse('Resposta veio como 'default' no 'switch'', Response::HTTP_OK);
                break;
        }

        return \$response;
    }
}";
    }
}