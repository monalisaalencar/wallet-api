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
    public function returnedFields()
    {
        $clients = Client::factory(2)->create([
            'type' => 'common'
        ]);
        foreach ($clients as $client) {
            Wallet::factory()->create([
                'balance_value' => 10000,
                'client_id' => $client->_id
            ]);
        }
        $transaction = Transaction::factory()->create([
            'payee_id' => $clients[0]->id,
            'payer_id' => $clients[1]->id
        ]);
        $this->json(
            $this->http_verb,
            $this->route . "/{$transaction->_id}",
        );
        $this->assertResponseOk();

        $transaction->forceDelete();
        foreach ($clients as $client) {
            $client->forceDelete();
        }
        Wallet::where('client_id', $clients->pluck('_id')->toArray())->delete();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
