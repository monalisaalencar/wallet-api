<?php

namespace tests\Controllers\ClientController\Tests;

use App\Models\Client;
use Tests\TestCase;

class UpdateTest extends TestCase
{
	protected $http_verb = 'PUT';
	protected $route = 'clients';

    protected function setUp(): void
    {
        parent::setUp();
	}

    /**
     * @test
     */
    public function clientNotFound()
    {
        $client_id = 'notfound';

        $this->json(
            $this->http_verb,
            $this->route . "/{$client_id}",
            [],
        );

        $this->assertResponseStatus(400);
    }

    /**
     * @test
     */
    public function responseAndStructure()
    {
        $client = Client::factory()->create();
        $this->json(
            $this->http_verb,
            $this->route . "/{$client->_id}",
            [],
        );
        $this->assertResponseOk();
        $this->seeJsonStructure([
            'client' => [
                '_id',
                'name',
                'type',
                'email_address',
                'cpf_cnpj',
            ]
        ]);
        $client->forceDelete();
    }

	protected function tearDown(): void
	{
		parent::tearDown();
	}
}
