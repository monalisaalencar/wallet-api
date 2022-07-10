<?php

namespace Tests\Controllers\ClientController\Providers;

trait CreateProviders
{
    public function payload()
    {
        return [
            'name invalid' => [
                'payload' => [
                        'cpf_cnpj' => '84002695000',
                        'type' => 'common',
                        'email_address' => 'teste@test.com',
                        'password' => '123456'
                ],
                'status' => 422,
            ],
            'cpf_cnpj require' => [
                'payload' => [
                        'name' => 'Test Test',
                        'type' => 'shopkeeper',
                        'email_address' => 'teste@test.com',
                        'password' => '123456'
                ],
                'status' => 422,
            ],
            'type require' => [
                'payload' => [
                        'name' => 'Test Test',
                        'cpf_cnpj' => '84002695000',
                        'email_address' => 'teste@test.com',
                        'password' => '123456'
                ],
                'status' => 422,
            ],
            'email require' => [
                'payload' => [
                        'name' => 'Test Test',
                        'cpf_cnpj' => '00824037000134',
                        'type' => 'shopkeeper',
                        'password' => '123456'
                ],
                'status' => 422,
            ],
            'email format invalid' => [
                'payload' => [
                        'name' => 'Test Test',
                        'cpf_cnpj' => '84002695000',
                        'type' => 'common',
                        'email_address' => 'testestest',
                        'password' => '123456'
                ],
                'status' => 422,
            ],
            'password require' => [
                'payload' => [
                        'name' => 'Test Test',
                        'cpf_cnpj' => '00824037000134',
                        'type' => 'shopkeeper',
                        'email_address' => 'testes@test.com',
                ],
                'status' => 422,
            ],
            'create valid' => [
                'payload' => [
                        'name' => 'Test Test',
                        'cpf_cnpj' => '00824037000134',
                        'type' => 'shopkeeper',
                        'email_address' => 'testes@test.com',
                        'password' => '123456'
                ],
                'status' => 201,
            ],
        ];
    }

    public function documentInvalidAndValid()
    {
        return [
            'cnpj invalid' => [
                'payload' => [
                        'name' => 'Monalisa Alencar',
                        'cpf_cnpj' => '11111111111512',
                        'type' => 'shopkeeper',
                        'email_address' => 'teste@test.com',
                        'password' => '123456'
                ],
                'message' => 'CPF/CNPJ Inválido',
                'status' => 400
            ],
            'cpf invalid' => [
                'payload' => [
                        'name' => 'Monalisa Alencar',
                        'cpf_cnpj' => '11111111111512',
                        'type' => 'common',
                        'email_address' => 'teste@test.com',
                        'password' => '123456'
                ],
                'message' => 'CPF/CNPJ Inválido',
                'status' => 400
            ],
            'cnpj valid' => [
                'payload' => [
                        'name' => 'Monalisa Alencar',
                        'cpf_cnpj' => '00824037000134',
                        'type' => 'shopkeeper',
                        'email_address' => 'teste@test.com',
                        'password' => '123456'
                ],
                'message' => 'Novo usuário criado',
                'status' => 201
            ],
            'cpf valid' => [
                'payload' => [
                        'name' => 'Monalisa Alencar',
                        'cpf_cnpj' => '84002695000',
                        'type' => 'common',
                        'email_address' => 'teste@test.com',
                        'password' => '123456'
                ],
                'message' => 'Novo usuário criado',
                'status' => 201
            ],
        ];
    }

    public function duplicateClient()
    {
        return [
            'client valid' => [
                'payload' => [
                        'name' => 'Monalisa Alencar',
                        'cpf_cnpj' => '84002695000',
                        'type' => 'common',
                        'email_address' => 'teste@test.com',
                        'password' => '123456'
                ],
                'message' => 'Já existe uma conta associada a este endereço de email ou cpf/cnpj',
            ]
        ];
    }
}
