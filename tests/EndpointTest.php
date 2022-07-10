<?php

namespace Tests;

use Tests\TestCase;

final class EndpointTest extends TestCase
{
    public function testIfTheCorrectTextIsBeingReturned()
    {
        $response = $this->call('GET', '/');

        $this->assertEquals(
            $response->getContent(),
            'WALLET-API'
        );
    }
}
