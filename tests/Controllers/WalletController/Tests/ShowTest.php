<?php

namespace tests\Controllers\WalletController\Tests;

use App\Models\Client;
use App\Models\Wallet;
use Tests\TestCase;

class ShowTest extends TestCase
{
    protected $http_verb = 'GET';
    protected $route = 'wallets';

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function walletNotFound()
    {
        $wallet_id = 'notfound';

        $this->json(
            $this->http_verb,
            $this->route . "/{$wallet_id}",
            [],
        );

        $this->assertResponseStatus(400);
    }

    /**
     * @test
     */
    public function returnedFields()
    {
        $client = Client::factory()->create([
            'type' => 'common'
        ]);
        $wallet = Wallet::factory()->create([
            'client_id' => $client->_id,
            'balance_value' => 100000
        ]);
        $this->json(
            $this->http_verb,
            $this->route . "/{$client->_id}",
        );
        $this->assertResponseOk();
        $this->seeJsonStructure([
            'wallet' => [
                'client_id',
                'balance_value',
                'client',
            ]
        ]);
        $wallet->forceDelete();
        $client->forceDelete();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
