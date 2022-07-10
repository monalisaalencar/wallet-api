<?php

namespace tests\Controllers\ClientController\Tests;

use App\Models\Client;
use Tests\TestCase;

class IndexTest extends TestCase
{
	protected $http_verb = 'GET';
	protected $route = 'clients/';

	protected function setUp(): void
	{
		parent::setUp();
	}

    /**
     * @test
     * @testWith ["page", "abc"]
     *           ["per_page", "kkk"]
     */
    public function invalidFields($param, $value)
    {
        $this->json(
            $this->http_verb,
            $this->route,
            [$param => $value],
        );

        $this->assertResponseStatus(422);
    }

    /**
     * @test
     * @testWith ["page", 1]
     *           ["per_page", 1]
     */
    public function validFields($param, $value)
    {
        $clients = Client::factory(3)->create();
        $this->json(
            $this->http_verb,
            $this->route,
            [$param => $value],
        );
        $this->assertResponseOk();
        $this->seeJsonStructure([
            'clients' => [
                [
                    'id',
                    'name',
                    'type',
                    'email_address',
                    'cpf_cnpj',
                ]
            ],
            'pages'
        ]);
        foreach ($clients as $client) {
            $client->forceDelete();
        }
    }

	protected function tearDown(): void
	{
		parent::tearDown();
	}
}
