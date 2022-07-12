<?php

namespace tests\Controllers\TransactionController\Tests;

use App\Models\Client;
use App\Models\Transaction;
use App\Models\Wallet;
use Tests\TestCase;

class RefoundTest extends TestCase
{
    protected $http_verb = 'POST';
    protected $route = 'transactions';

    protected function setUp(): void
    {
        parent::setUp();
        $this->clients = Client::factory(2)->create([
            'type' => 'common'
        ]);
        foreach ($this->clients as $client) {
            Wallet::factory()->create([
                'balance_value' => 10000,
                'client_id' => $client->_id
            ]);
        }
        $this->payee_id = $this->clients[0]->id;
        $this->payer_id = $this->clients[1]->id;

        $this->transaction = Transaction::factory()->create([
            'payee_id' => $this->payee_id,
            'payer_id' => $this->payer_id,
            'total_value' => 200
        ]);
    }

    /**
     * @test
     */
    public function transactionNotFound()
    {
        $transaction_id = 'notfound';

        $this->json(
            $this->http_verb,
            $this->route . "/{$transaction_id}",
            [],
        );

        $this->assertResponseStatus(400);
    }

    /**
     * @test
     */
    public function returnedFieldsWithValuesValid()
    {
        $this->json(
            $this->http_verb,
            $this->route . "/{$this->transaction->_id}",
        );

        $this->assertResponseOk();

        $wallet_payer = Wallet::where('client_id', $this->payer_id)->first();
        $this->assertEquals(10000 + 200, $wallet_payer->balance_value);

        $wallet_payee = Wallet::where('client_id', $this->payee_id)->first();
        $this->assertEquals(10000 - 200, $wallet_payee->balance_value);
    }

    /**
     * @test
     */
    public function replyWithSuccessMessage()
    {
        $response = $this->json(
            $this->http_verb,
            $this->route . "/{$this->transaction->_id}",
        );

        $this->assertResponseOk();

        $content = json_decode($response->response->content(), true);

        $this->assertEquals($content['message'], 'Transação reembolsada com sucesso!');
    }

    protected function tearDown(): void
    {
        Wallet::whereIn('client_id', $this->clients->pluck('_id')->toArray())->forceDelete();
        $this->transaction->forceDelete();
        foreach ($this->clients as $client) {
            $client->forceDelete();
        }
        parent::tearDown();
    }
}
