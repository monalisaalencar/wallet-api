<?php

use GuzzleHttp\Client as GuzzleClient;

if (!function_exists('authenticationRequest')) {
    function authenticationRequest($request, $route, $body)
    {
        $client = new GuzzleClient([
            'base_uri' => env('AUTHENTICATION_ENDPOINT'),
        ]);

        $response = $client->$request($route, [
            'json' => $body,
            'http_errors' => false,
        ]);

        $message = $response->getBody()->getContents();

        return [
            'status' => $response->getStatusCode(),
            'response' => $message,
        ];
    }
}
