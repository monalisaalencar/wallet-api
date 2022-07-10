<?php

namespace Tests\Controllers\ClientController\Tests;

use App\Models\Client;
use Tests\TestCase;
use Tests\Controllers\ClientController\Providers\CreateProviders;

class CreateTest extends TestCase
{
    use CreateProviders;

    protected $http_verb = 'POST';
    protected $route = 'clients';

    function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @dataProvider payload
     */
    public function createValidate($payload, $status)
    {
        $response = $this->json(
            $this->http_verb,
            $this->route,
            $payload,
        );

        $this->assertResponseStatus($status);

        $content = json_decode($response->response->content(), true);
        $client_id = isset($content['client']) ? $content['client']['id'] : '';
        Client::where('_id', $client_id)->forceDelete();
    }

    /**
     * @test
     * @dataProvider duplicateClient
     */
    public function createDuplicateClient($payload, $message)
    {
        $client = Client::factory()->create($payload);

        $response = $this->json(
            $this->http_verb,
            $this->route,
            $payload,
        );

        $this->assertResponseStatus(400);
        $response->seeJsonContains([
            'message' => $message
        ]);
        $client->forceDelete();
    }

    /**
     * @test
     * @dataProvider documentInvalidAndValid
     */
    public function createWithDocumentValid($payload, $message, $status)
    {
        $response = $this->json(
            $this->http_verb,
            $this->route,
            $payload,
        );

        $this->assertResponseStatus($status);
        $response->seeJsonContains([
            'message' => $message
        ]);

        $content = json_decode($response->response->content(), true);
        $client_id = isset($content['client']) ? $content['client']['id'] : '';
        Client::where('_id', $client_id)->forceDelete();
    }

    function tearDown(): void
    {
        parent::tearDown();
    }
}
