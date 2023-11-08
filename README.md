# trabalho_frameworks

## Gere o arquivo .env

```bash
cp .env.example .env
```


## Definir as variáveis do .env

```bash
DB_CONNECTION=mysql

DB_HOST=db

DB_PORT=3306

DB_DATABASE=trabalho_frameworks

DB_USERNAME=root

DB_PASSWORD=password
```


## Construa a imagem com o docker build

```bash
docker compose build
```


## Execute os comandos no container app

```bash
docker compose exec app composer install

docker compose exec app npm i

docker compose exec app npm run dev

docker compose exec app optimize.cmd

docker compose exec app php artisan migrate:refresh --seed
```
