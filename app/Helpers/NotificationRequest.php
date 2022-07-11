<?php

use GuzzleHttp\Client as GuzzleClient;

if (!function_exists('notificationRequest')) {
    function notificationRequest($request, $route, $body)
    {
        $client = new GuzzleClient([
            'base_uri' => env('NOTIFICATION_ENDPOINT'),
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
