<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * cria carteira digital para o cliente
     *
     * @param Request $request  Requisição
     * @return void
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required|string',
            'balance_value' => 'required|numeric',
        ]);

        $new_wallet = Wallet::create([
            'client_id' => $request['client_id'],
            'balance_value' => $request['balance_value']
        ]);

        return response([
            'transaction' => [
                'id' => $new_wallet->_id,
            ],
            'message' => 'Nova carteira criada com sucesso!',
        ], 201);
    }

    /**
     * exibe a dados da carteira do client
     *
     * @param string $id identificador do cliente
     * @return void
     */
    public function show(string $id)
    {
        $wallet = Wallet::where('client_id', $id)
            ->with('client')
            ->first();

        if (empty($wallet)) {
            return response([
                'message' => 'Carteira não encontrada',
            ], 400);
        }

        return response([
            'wallet' => $wallet->toArray(),
        ], 200);
    }
}
