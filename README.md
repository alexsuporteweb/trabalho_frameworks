# Projeto Final - Desenvolvimento de Software Através de Frameworks Laravel

- **Biblioteca/Framework:** [Nome e link da biblioteca/framework](http://google.com)
- **Tecnologias aplicadas:** Lista de linguagens utilizadas.
- **Integrantes:**
  - Alex
  - Joeder
  - Rafael
  - Rodolfo
  - Wdson

## 1. Descrição da biblioteca/framework

Laravel é um framework PHP livre e open-source criado por Taylor B. Otwell para o desenvolvimento de sistemas web que utilizam o padrão MVC (model, view, controller). Algumas características proeminentes do Laravel são sua sintaxe simples e concisa, um sistema modular com gerenciador de dependências dedicado, várias formas de acesso a banco de dados relacionais e vários utilitários indispensáveis no auxílio ao desenvolvimento e manutenção de sistemas. Para fazer o uso desse framework, utilizado a ferramente Laragon que nos entrega um ambiente WEB simples e completo, para desenvolvermos nossas aplicações.A edição completa inclui Apache, MySQL, PHP, Node.js, npm, yarn, git, compositor e outras ferramentas.

## 2. Descrição do problema

Desenvolver uma aplicação que abranja todos os municípios do Brasil é um desafio significativo, especialmente se você optar por cadastrar manualmente todas essas informações. Essa tarefa envolve um grande volume de dados, mas é possível realizar o processo de forma estruturada.

Primeiramente, é crucial organizar a coleta de dados de maneira eficiente. Recomenda-se utilizar fontes confiáveis, como bases de dados governamentais, para garantir a precisão das informações. Portais oficiais, como o IBGE (Instituto Brasileiro de Geografia e Estatística), podem fornecer dados detalhados sobre todos os municípios brasileiros, incluindo informações demográficas, geográficas e administrativas.

## 3. Solução

O Laravel, por ser um framework PHP robusto e altamente utilizado, oferece recursos que facilitam a integração com APIs externas, tornando o processo mais eficiente.

O IBGE disponibiliza uma API de localidades que fornece dados detalhados sobre municípios, estados, regiões, entre outros. Para utilizar essa API, você precisará obter uma chave de acesso no site do IBGE.

Os códigos abaixos ilustram as estapas para popular o Banco de Dados com as inforações originadas na API do IBGE:

No Laravel utilizamos as classes seeders para popular o Banco de Dados no início da aplicação.

Neste projeto criamos a classe MunicipioSeeder.php para popularmos o Banco de Dados dos municípios. Assim nessa classe no método run é chamado o método executar da classe ImportarMunicipios.php.


```php

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

Abaixo segue a classe Municipio.php do tipo model. e Assim é realizado a inclusão no banco de dados na tabela municipio.

```php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipios extends Model
{
    use HasFactory;
}

```

Esse padrão é realizado para popular inicialmente todas as tabelas do banco de dados referente aos dados obtidos da API do IGBE e outras tabelas como: das regiões, microregiões, estados, etc...

Após termos nosso banco de dados atualizados com todas a informações, então passamos a desenvolver o objetivo da API que é busca desees dados. Abaixo é mostrado a reliazação da consulta a API de busca de dados dos municípios.

No arquivo Web.php é cadastrada todas as rotas da API e fazermos a busca do município, sendo um requisição do tipo GET. No laravel temos a possibilidade de criar grupos de rotas, como abaixo é criado o grupo de rota municipios e depois é criados os endpoints. Para acessarmos a consulta aos municipios utilizamos o endereço: http://trabalho_frameworks.test/municipio.

```php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('municipio')->group(function () {
    Route::get('/', [App\Http\Controllers\MunicipioController::class, 'index'])->name('municipio-index');
    Route::post('/update/{id}', [App\Http\Controllers\MunicipioController::class, 'update'])->name('municipio-update');
    Route::get('/delete/{id}', [App\Http\Controllers\MunicipioController::class, 'destroy'])->name('municipio-delete');
});
```
Apos fazermos a requisição do tipo GET: http://trabalho_frameworks.test/municipio é redirecinado a classe 





## 4. Conclusão

Ao seguir esses passos, você terá uma solução Laravel eficiente que integra a API de localidades do IBGE, permitindo a importação e atualização contínua dos dados dos municípios brasileiros em seu aplicativo. Certifique-se de manter a documentação do Laravel e do IBGE à mão para referência adicional durante o desenvolvimento.

## 5. Referências

https://laravel.com/
https://laragon.org/
https://laravel-docs-pt-br.readthedocs.io/en/latest/eloquent/
