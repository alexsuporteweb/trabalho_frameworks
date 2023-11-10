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

Laravel é um framework PHP livre e open-source criado por Taylor B. Otwell para o desenvolvimento de sistemas web que utilizam o padrão MVC (model, view, controller). Algumas características proeminentes do Laravel são sua sintaxe simples e concisa, um sistema modular com gerenciador de dependências dedicado, várias formas de acesso a banco de dados relacionais e vários utilitários indispensáveis no auxílio ao desenvolvimento e manutenção de sistemas. 

## 2. Descrição do problema

Desenvolver uma aplicação que abranja todos os municípios do Brasil é um desafio significativo, especialmente se você optar por cadastrar manualmente todas essas informações. Essa tarefa envolve um grande volume de dados, mas é possível realizar o processo de forma estruturada.

Primeiramente, é crucial organizar a coleta de dados de maneira eficiente. Recomenda-se utilizar fontes confiáveis, como bases de dados governamentais, para garantir a precisão das informações. Portais oficiais, como o IBGE (Instituto Brasileiro de Geografia e Estatística), podem fornecer dados detalhados sobre todos os municípios brasileiros, incluindo informações demográficas, geográficas e administrativas.

## 3. Solução

O Laravel, por ser um framework PHP robusto e altamente utilizado, oferece recursos que facilitam a integração com APIs externas, tornando o processo mais eficiente.

O IBGE disponibiliza uma API de localidades que fornece dados detalhados sobre municípios, estados, regiões, entre outros. Para utilizar essa API, você precisará obter uma chave de acesso no site do IBGE.

O código abaixo ilustra a criação de método (Service) usado para obter os dados dos municípois:

Exemplo ilustrtivo

```php
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
        DB::beginTransaction();
        try {
            $dados = json_decode(
                Http::get(
                    $this->apiIbgeLocalidadesUrl . '/municipios'
                )->body()
            );

            if ($dados) :
                foreach ($dados as $dado) :
                    $id = $dado->id;
                    $nome = $dado->nome;
                    $microregiao_id = $dado->microrregiao->id;
                    $mesorregiao_id = $dado->microrregiao->mesorregiao->id;
                    $estado_id = $dado->microrregiao->mesorregiao->UF->id;
                    $regiao_id = $dado->microrregiao->mesorregiao->UF->regiao->id;
                    $regiao_imediata_id = $dado->{"regiao-imediata"}->id;
                    $regiao_intermediaria_id = $dado->{"regiao-imediata"}->{"regiao-intermediaria"}->id;
                    $retorno = $this->municipios::updateOrCreate(
                        [
                            'id' => $id
                        ],
                        [
                            'nome' => $nome,
                            'microregiao_id' => $microregiao_id,
                            'mesorregiao_id' => $mesorregiao_id,
                            'estado_id' => $estado_id,
                            'regiao_id' => $regiao_id,
                            'regiao_imediata_id' => $regiao_imediata_id,
                            'regiao_intermediaria_id' => $regiao_intermediaria_id,
                        ]
                    );
                endforeach;
            endif;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Erro durante consulta de API', ['erro' => $th->getMessage()]);
            throw new Exception($th->getMessage(), 1);
        }
    }
}
```


## 4. Conclusão

Ao seguir esses passos, você terá uma solução Laravel eficiente que integra a API de localidades do IBGE, permitindo a importação e atualização contínua dos dados dos municípios brasileiros em seu aplicativo. Certifique-se de manter a documentação do Laravel e do IBGE à mão para referência adicional durante o desenvolvimento.

## 5. Referências

Inclua a lista de referências utilizadas no projeto ou outras referências interessantes que tiver encontrado e que possam ser úteis para os colegas ao explorar esta ferramenta.