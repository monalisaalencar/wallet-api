<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    const TYPE_VALID_CLIENT = ['common', 'shopkeeper'];
    protected $default_per_page = 10;

    /**
     * cria client
     *
     * @param Request $request  Requisição
     * @return void
     */
    public function create(Request $request)
    {
        $this->isValidContentClient($request);

        $new_client = Client::create([
            'name' => $request['name'],
            'email_address' => $request['email_address'],
            'password' => md5($request['password']),
            'cpf_cnpj' => $request->cpf_cnpj,
            'type' => $request->type,
        ]);

        return response([
            'client' => [
                'id' => $new_client->_id,
            ],
            'message' => 'Novo usuário criado',
        ], 201);
    }

    /**
     * valida se o cliente pode ser criado
     *
     * @param Request $request  Requisição
     * @return void
     */
    private function isValidContentClient(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email_address' => 'required|email',
            'password' => 'required|string',
            'type' => [
                'required',
                Rule::in(self::TYPE_VALID_CLIENT),
            ],
            'cpf_cnpj' => 'required|string'
        ]);

        $client_valid = Client::where('email_address', $request->email_address)->orWhere('cpf_cnpj', $request->cpf_cnpj)->count();

        if ($client_valid) {
            return response([
                'message' => 'Já existe uma conta associada a este endereço de email ou cpf/cnpj',
            ], 400)->throwResponse();
        }

        if (($request->type == 'common' && !validate_cpf($request->cpf_cnpj))
            || ($request->type == 'shopkeeper' && !validate_cnpj($request->cpf_cnpj))) {
            return response([
                'message' => 'CPF/CNPJ Inválido'
            ], 400)->throwResponse();
        }
    }

    /**
     * exibe cliente
     *
     * @param string $id  identificador do cliente
     * @return void
     */
    public function show(string $id)
    {
        $client = Client::where('_id', $id)->first();

        if (empty($client)) {
            return response([
                'message' => 'Cliente não encontrado',
            ], 400);
        }

        return response([
            'client' => $client->toArray(),
        ], 200);
    }

    /**
     * atualiza um cliente
     *
     * @param Request $request  Requisição
     * @param string $id  identificador do cliente
     * @return void
     */
    public function update(Request $request, string $id)
    {
        $client = Client::where('_id', $id)->first();

        if (!$client) {
            return response([
                'mensage' => 'Cliente não encontrado',
            ], 400);
        }

        foreach ($request->all() as $key => $value) {
            if (isset($client->$key)) {
                $client->$key = $value;
            }
        }

        $client->save();

        return response([
            'client' => $client,
        ], 200);
    }

    /**
     * listagem de clientes
     *
     * @param Request $request  Requisição
     * @return void
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'page' => 'numeric|min:1',
            'per_page' => 'numeric|min:1',
        ]);

        $per_page = $request->per_page ?? $this->default_per_page;

        $clients = Client::query()
        ->paginate(
            $per_page,
            ['*'],
            'page',
            $request->page
        );

        return response([
            'clients' => ClientResource::collection($clients->items()),
            'pages' => ceil($clients->total() / $per_page),
        ], 200);
    }

    /**
     * deleta cliente
     *
     * @param Request $request  Requisição
     * @return void
     */
    public function delete(string $id)
    {
        $client = Client::where('_id', $id)->first();

        if (!$client) {
            return response([
                'mensage' => 'Cliente não encontrado',
            ], 400);
        }

        $client->delete();

        return response([
            'message' => 'Cliente deletado com sucesso!',
        ], 200);
    }
}
