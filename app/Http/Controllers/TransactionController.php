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

        $this->applyValueTrasaction($payer->wallet, $payee->wallet, $request->total_value);

        notificationRequest('GET', '', []);

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

        if (empty($payer) || $payer->type == 'merchant') {
            return response([
                'message' => 'Usuário indisponível para realização de tranferência!',
            ], 400)->throwResponse();
        }

        if ($payer->wallet->balance_value < $request->total_value) {
            return response([
                'message' => 'Saldo insuficiente.',
            ], 400)->throwResponse();
        }

        $response = authorizationRequest('GET', '', []);

        if ($response['status'] != 200) {
            return response([
                'message' => 'Transação não autorizado',
            ], 400)->throwResponse();
        }
    }

    /**
     * atualiza a carteira dos clientes com o valor da transação
     *
     * @param collect $wallet_payer  carteira do pagador
     * @param collect $wallet_payee  carteira do beneficiário
     * @param double $total_value  valor da transação
     * @return void
     */
    private function applyValueTrasaction($wallet_payer, $wallet_payee, $total_value, $refound = false)
    {
        if ($refound) {
            $wallet_payer->balance_value += $total_value;
            $wallet_payee->balance_value -= $total_value;
        } else {
            $wallet_payer->balance_value -= $total_value;
            $wallet_payee->balance_value += $total_value;
        }

        $wallet_payee->save();
        $wallet_payer->save();
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

        $this->applyValueTrasaction($payer->wallet, $payee->wallet, $transaction->total_value, true);

        $transaction->delete();

        return response([
            'message' => 'Transação reembolsada com sucesso!',
        ], 200);
    }
}
