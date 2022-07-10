<?php

namespace tests\Controllers\ClientController\Tests;

use App\Models\Client;
use Tests\TestCase;

class DeleteTest extends TestCase
{
	protected $http_verb = 'DELETE';
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
    public function deleteField()
    {
        $client = Client::factory()->create();
        $response = $this->json(
            $this->http_verb,
            $this->route . "/{$client->_id}",
            [],
        );
        $this->assertResponseOk();
        $response->seeJsonContains([
            'message' => 'Cliente deletado com sucesso!'
        ]);
    }

	protected function tearDown(): void
	{
		parent::tearDown();
	}
}
