<?php

namespace Tests\Controllers\TransactionController\Providers;

trait CreateProviders
{
    public function payload()
    {
        return [
            'total value require' => [
                'payload' => [
                        'payer_id' => '62cb68c9b18d8913e226f1eq',
                        'payee_id' => '62cb68c9b18d8913e226f1eq',
                ],
                'status' => 422,
            ],
            'payer_id require' => [
                'payload' => [
                    'total_value' => '84002695000',
                    'payee_id' => '62cb68c9b18d8913e226f1eq',
                ],
                'status' => 422,
            ],
            'payee_id require' => [
                'payload' => [
                    'total_value' => '84002695000',
                    'payer_id' => '62cb68c9b18d8913e226f1eq',
                ],
                'status' => 422,
            ]
        ];
    }
}
