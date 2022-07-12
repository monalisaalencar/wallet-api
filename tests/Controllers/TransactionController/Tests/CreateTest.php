<?php

namespace Tests\Controllers\TransactionController\Tests;

use App\Models\Client;
use App\Models\Transaction;
use App\Models\Wallet;
use Tests\TestCase;
use Tests\Controllers\TransactionController\Providers\CreateProviders;

class CreateTest extends TestCase
{
    use CreateProviders;

    protected $http_verb = 'POST';
    protected $route = 'transactions';

    function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @dataProvider payload
     */
    public function createInvalidPayload($payload, $status)
    {
        $this->json(
            $this->http_verb,
            $this->route,
            $payload,
        );

        $this->assertResponseStatus($status);
    }

    /**
     * @test
     */
    public function createWithValuesValid()
    {
        $payer = Client::factory()->create([
            'type' => 'common'
        ]);
        $payee = Client::factory()->create([
            'type' => 'merchant'
        ]);

        Wallet::factory()->create([
            'balance_value' => 10000,
            'client_id' => $payee->_id
        ]);

        Wallet::factory()->create([
            'balance_value' => 10000,
            'client_id' => $payer->_id
        ]);

        $payload = [
            "total_value" => 100,
            "payer_id" => $payer->_id,
            "payee_id" => $payee->_id
        ];

        $this->json(
            $this->http_verb,
            $this->route,
            $payload,
        );

        $this->assertResponseOk();

        $wallet_payer = Wallet::where('client_id', $payer->_id)->first();
        $this->assertEquals(10000 - $payload['total_value'], $wallet_payer->balance_value);

        $wallet_payee = Wallet::where('client_id', $payee->_id)->first();
        $this->assertEquals(10000 + $payload['total_value'], $wallet_payee->balance_value);

        $payer->forceDelete();
        $payee->forceDelete();
        $wallet_payer->forceDelete();
        $wallet_payee->forceDelete();
        Transaction::where('payer_id', $payer->_id)->where('payee_id', $payee->_id)->forceDelete();
    }

    function tearDown(): void
    {
        parent::tearDown();
    }
}
