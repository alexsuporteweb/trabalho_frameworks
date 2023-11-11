# Projeto Final - Desenvolvimento de Software Através da Framework Laravel

- **Biblioteca/Framework:** [Laravel - The PHP Framework For Web Artisans](https://laravel.com/)
- **Tecnologias aplicadas:** PHP
- **Integrantes:**
  - Alex Lauro Bueno Gomes
  - Joeder
  - Rafael
  - Rodolfo
  - Wdson

## 1. Laravel - PHP Framework

Laravel é um framework PHP livre e open-source criado por Taylor B. Otwell para o desenvolvimento de sistemas web que utilizam o padrão MVC (model, view, controller). Algumas características proeminentes do Laravel são sua sintaxe simples e concisa, um sistema modular com gerenciador de dependências dedicado, várias formas de acesso a banco de dados relacionais e vários utilitários indispensáveis no auxílio ao desenvolvimento e manutenção de sistemas. Para fazer o uso desse framework, utilizado a ferramente Laragon que nos entrega um ambiente WEB simples e completo, para desenvolvermos nossas aplicações.A edição completa inclui Apache, MySQL, PHP, Node.js, npm, yarn, git, compositor e outras ferramentas.

## 2. Descrição do problema

Durante o desenvolvimento de uma aplicação surgiu a nescessidade de se cadastrar e manter atualizado um banco de dados com todos os municípios do Brasil. Um desafio significativo, especialmente se você optar por cadastrar manualmente todas essas informações. Essa tarefa envolve um grande volume de dados, mas é possível realizar o processo de forma estruturada.

Primeiramente, é crucial organizar a coleta de dados de maneira eficiente. Recomenda-se utilizar fontes confiáveis, como bases de dados governamentais, para garantir a precisão das informações. Portais oficiais, como o IBGE (Instituto Brasileiro de Geografia e Estatística), podem fornecer dados detalhados sobre todos os municípios brasileiros, incluindo informações demográficas, geográficas e administrativas.

## 3. Solução

O Laravel, por ser um framework PHP robusto e altamente utilizado, oferece recursos que facilitam a integração com APIs externas, tornando o processo mais eficiente.

O IBGE disponibiliza uma API de localidades que fornece dados detalhados sobre municípios, estados, regiões, entre outros. Para utilizar essa API, você precisará obter uma chave de acesso no site do IBGE.

Os códigos abaixos ilustram as estapas para popular o Banco de Dados com as inforações originadas na API do IBGE:

No Laravel utilizamos as classes seeders para popular o Banco de Dados no início da aplicação.

Neste projeto criamos a classe MunicipioSeeder.php para popularmos o Banco de Dados dos municípios. Assim nessa classe no método run é chamado o método executar() da classe ImportarMunicipios.php.


```php
<?php
namespace Database\Seeders;

use App\Actions\Imports\ImportarMunicipios;
use Exception;
use Illuminate\Database\Seeder;

class MunicipiosSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(ImportarMunicipios $importarMunicipios)
    {
        $importarMunicipios->executar();
    }
}
```

Já na classe ImportarMunicipio.php, no seu método construtor é configurado os dados de chamada da API do IBGE e na função executar() é feita a chamada da API IBGE, o recebimentos dos dados, tratados erros e chamado a classe municipio.php do tipo model para a inserção no banco de dados. 

```php
<?php
namespace App\Actions\Imports;

use App\Models\Municipios;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportarMunicipios
{
    private $municipios;
    private $apiIbgeLocalidadesUrl;

    public function __construct(Municipios $municipios)
    {
        $this->municipios = $municipios;
        $this->apiIbgeLocalidadesUrl = env('API_IBGE_LOCALIDADES_URL');
    }

    public function executar()
    {
        try {
            $url = $this->apiIbgeLocalidadesUrl . '/municipios';
            $data = Http::timeout(300)->retry(3, 1000)->get($url);

            $dados = json_decode(Http::get($url)->body(), true);

            if ($data->status() === 200) :
                foreach ($dados as $dado) :
                    $id = $dado['id'];
                    $nome = $dado['nome'];
                    $microrregiao_id = $dado['microrregiao']['id'];
                    $mesorregiao_id = $dado['microrregiao']['mesorregiao']['id'];
                    $estado_id = $dado['microrregiao']['mesorregiao']['UF']['id'];
                    $regiao_id = $dado['microrregiao']['mesorregiao']['UF']['regiao']['id'];
                    $regiao_imediata_id = $dado['regiao-imediata']['id'];
                    $regiao_intermediaria_id = $dado['regiao-imediata']['regiao-intermediaria']['id'];
                    $retorno = $this->municipios::updateOrCreate(
                        [
                            'id' => $id
                        ],
                        [
                            'nome' => $nome,
                            'microrregiao_id' => $microrregiao_id,
                            'mesorregiao_id' => $mesorregiao_id,
                            'estado_id' => $estado_id,
                            'regiao_id' => $regiao_id,
                            'regiao_imediata_id' => $regiao_imediata_id,
                            'regiao_intermediaria_id' => $regiao_intermediaria_id,
                        ]
                    );
                endforeach;
            else :
                return response()->json(['message' => 'Erro na solicitação. Status code:'], $dados()->status());
            endif;
        } catch (\Throwable $th) {
            Log::error('Erro durante consulta de API', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }
}
```

Abaixo segue a classe Municipio.php do tipo model e assim é realizado a inclusão no banco de dados na tabela municipio.

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipios extends Model
{
    use HasFactory;
}

```

Esse padrão é realizado para popular inicialmente todas as tabelas do banco de dados referente aos dados obtidos da API do IGBE e outras tabelas como: das regiões, microregiões, estados, etc...

Após termos nosso banco de dados atualizados com todas a informações, então passamos a desenvolver o objetivo da API que é busca desses dados. Abaixo é mostrado a realização da consulta a API de busca de dados dos municípios.

No arquivo Web.php é cadastrada todas as rotas da API e para realizarmos a busca do município, utilizamos uma requisição do tipo GET. No laravel temos a possibilidade de criar grupos de rotas, como abaixo é criado o grupo de rota municipios e depois são criados os endpoints. Para acessarmos a consulta aos municipios podemos utilizar o endereço: http://trabalho_frameworks.test/municipio no navegador ou podemos utlizar o insomnia para realizar as requisoções.

```php
<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('municipio')->group(function () {
    Route::get('/', [App\Http\Controllers\MunicipioController::class, 'index'])->name('municipio-index');
    Route::post('/update/{id}', [App\Http\Controllers\MunicipioController::class, 'update'])->name('municipio-update');
    Route::get('/delete/{id}', [App\Http\Controllers\MunicipioController::class, 'destroy'])->name('municipio-delete');
});
```
Apos fazermos a requisição do tipo GET: http://trabalho_frameworks.test/municipio/ é redirecinado a classe do tipo controller de nome MunicipioController.php. Na classe MunicipioControler.php é executado o método index(), conforme consta no arquivo Web.php para a realização da buscas de todos os municípios. Ja no método index() é instanciado a classe do tipo model de nome municipio.php que executa a busca no banco de dados, devolve os dados em formato json, e trata possíveis erros, conforme segue abaixo.

```php
<?php
namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use App\Models\Municipios;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

class MunicipioController extends Controller
{
    public function index()
    {
        try {
            $municipios = Municipios::all();
            if ($municipios->isEmpty()) {
                return response()->json(['message' => 'Listagem vazia!'], 200);
            }

            return response()->json($municipios, 200);
        } catch (\Throwable $th) {
            Log::error('Erro durante a execução', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }

```

E os demais endpoints também são criados e executados seguindo esse mesmo padrão utilizando o laravel. 

## 4. Conclusão

Ao seguir esses passos, você terá uma solução Laravel eficiente que integra a API de localidades do IBGE, permitindo a importação e atualização contínua dos dados dos municípios brasileiros em seu aplicativo. Certifique-se de manter a documentação do Laravel e do IBGE à mão para referência adicional durante o desenvolvimento.

## 5. Referências

* https://laravel.com/
* https://laragon.org/
* https://laravel-docs-pt-br.readthedocs.io/en/latest/eloquent/


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

## Crie os containers com docker compose

```bash
docker compose up -d
```


## Execute os comandos no container app

```bash
docker compose exec app composer install

docker compose exec app npm i

docker compose exec app npm run dev

docker compose exec app optimize.cmd

docker compose exec app php artisan migrate:refresh --seed
```
