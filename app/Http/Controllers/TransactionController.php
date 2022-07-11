<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Client;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * cria uma transação de um cliente
     *
     * @param Request $request  Requisição
     * @return void
     */
    public function create(Request $request)
    {
        $payer = Client::where('_id', $request->payer_id)
        ->with('wallet')
        ->first();

        $this->isValidContentTransaction($request, $payer);

        $payee = Client::where('_id', $request->payee_id)
        ->with('wallet')
        ->first();

        if (empty($payee)) {
            return response([
                'message' => 'Beneficiário não está disponível.',
            ], 400);
        }

        $new_transaction = Transaction::create([
            'payee_id' => $request['payee_id'],
            'payer_id' => $request['payer_id'],
            'total_value' => $request['total_value']
        ]);

        $wallet_payee = $payee->wallet;
        $wallet_payer = $payer->wallet;

        $wallet_payer->balance_value -= $request->total_value;
        $wallet_payee->balance_value += $request->total_value;

        $wallet_payee->save();
        $wallet_payer->save();

        //notificationRequest('GET', '', []);

        return response([
            'transaction' => [
                'id' => $new_transaction->_id,
            ],
            'message' => 'Transação Realizada!',
        ], 200);
    }

    /**
     * valida se a trasação pode ser criada
     *
     * @param Request $request  Requisição
     * @return void
     */
    private function isValidContentTransaction(Request $request, $payer)
    {
        $this->validate($request, [
            'payee_id' => 'required|string',
            'payer_id' => 'required|string',
            'total_value' => 'required',
        ]);

        if (empty($payer) || $payer->type == 'shopkeeper') {
            return response([
                'message' => 'Usuário indisponível para realização de tranferência!',
            ], 400)->throwResponse();
        }

        if ($payer->wallet->balance_value < $request->total_value) {
            return response([
                'message' => 'Saldo insuficiente.',
            ], 400)->throwResponse();
        }

        // $response = authenticationRequest('GET', '', []);

        // if ($response['status'] != 200) {
        //     return response([
        //         'message' => 'Transação não autorizado',
        //     ], 400)->throwResponse();
        // }
    }

    /**
     * exibe uma trasação
     *
     * @param string $id  identificador da transação
     * @return void
     */
    public function show(string $id)
    {
        $transaction = Transaction::where('_id', $id)
            ->with(['payer','payee'])
            ->first();

        if (empty($transaction)) {
            return response([
                'message' => 'Trasação não encontrado',
            ], 400);
        }

        return response([
            'transaction' => $transaction->toArray(),
        ], 200);
    }

    /**
     * listagem de transações de um cliente
     *
     * @param Request $request  Requisição
     * @return void
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'page' => 'numeric|min:1',
            'per_page' => 'numeric|min:1'
        ]);

        $per_page = $request->per_page ?? $this->default_per_page;

        $transactions = Transaction::query()
        ->paginate(
            $per_page,
            ['*'],
            'page',
            $request->page
        );

        return response([
            'transactions' => TransactionResource::collection($transactions->items()),
            'pages' => ceil($transactions->total() / $per_page),
        ], 200);
    }

    /**
     * reembolsa o valor de uma trasação
     *
     * @param Request $id  identificador da trasação
     * @return void
     */
    public function refound(string $id)
    {
        $transaction = Transaction::where('_id', $id)->first();
        if (empty($transaction)) {
            return response([
                'message' => 'Trasação não encontrado',
            ], 400);
        }

        $payer = Client::where('_id', $transaction->payer_id)
        ->with('wallet')
        ->first();

        $payee = Client::where('_id', $transaction->payee_id)
        ->with('wallet')
        ->first();

        $wallet_payee = $payee->wallet;
        $wallet_payer = $payer->wallet;

        $wallet_payer->balance_value += $transaction->total_value;
        $wallet_payee->balance_value -= $transaction->total_value;

        $wallet_payee->save();
        $wallet_payer->save();

        $transaction->delete();

        return response([
            'message' => 'Transação reembolsada com sucesso!',
        ], 200);
    }
}
