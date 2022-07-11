# Wallet-api



## Tecnologias

- [Lumen 8](https://lumen.laravel.com/docs/8.x)
- [Lumen Generator](https://github.com/flipboxstudio/lumen-generator)
- [MongoDB](https://www.mongodb.com/) com [jenssegers/laravel-mongodb](https://github.com/jenssegers/laravel-mongodb)
- [PHPUnit](https://phpunit.de)
- [Docker](https://www.docker.com/) com [compose](https://docs.docker.com/compose/)

## Instruções

Antes de prosseguir, tenha certeza de ter o [docker](https://docs.docker.com/) devidamente instalado
e configurado em sua máquina, juntamente com o [compose](https://docs.docker.com/compose).

### Instalação

1. Baixe o projeto com git ou faça o download manualmente.

```bash
git clone https://github.com/monalisaalencar/wallet-api.git && cd wallet-api
```

2. Copie o arquivo `.env.example` e o renomeie para `.env`

```bash
cp .env.example .env
```

3. Suba o projeto utilizando `docker-compose`

```bash
docker-compose up -d
```

4. Instale as dependências

```bash
docker exec wallet_api composer install
```

Para prosseguir com os testes, devemos realizar a configuração completa do banco. Veja na próxima seção.

### Configuração do Banco

As variáveis de ambiente já estão configuradas no .env.example

### Testes

execute os testes dentro do container da API.

```
docker exec wallet_api vendor/bin/phpunit
```
