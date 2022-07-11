<?php

namespace tests\Controllers\TransactionController\Tests;

use App\Models\Client;
use App\Models\Transaction;
use Tests\TestCase;

class ShowTest extends TestCase
{
    protected $http_verb = 'GET';
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
        $transaction = Transaction::factory()->create([
            'payee_id' => $clients[0]->id,
            'payer_id' => $clients[1]->id
        ]);
        $this->json(
            $this->http_verb,
            $this->route . "/{$transaction->_id}",
        );
        $this->assertResponseOk();
        $this->seeJsonStructure([
            'transaction' => [
                '_id',
                'payee_id',
                'payer_id',
                'payer',
                'payee',
            ]
        ]);
        $transaction->forceDelete();
        foreach ($clients as $client) {
            $client->forceDelete();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
