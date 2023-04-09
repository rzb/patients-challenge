# Patients Challenge

[![codecov](https://codecov.io/gh/rzb/patients-challenge/branch/main/graph/badge.svg?token=SM5IGSGDUV)](https://codecov.io/gh/rzb/patients-challenge)

Renato Zuma Bange, Fullstack Developer



## Instalação

Após clonar e copiar os valores da .env.example...

Inicie o app (postgres, redis e nginx)
```console
docker-compose up -d --build app
```

Caso identifique conflito de porta, altere sua .env de acordo e indique-a ao reiniciar o app
```console
docker-compose --env-file=./src/.env up -d --build app
```

Inicie o elasticsearch:
```console
docker-compose up -d --build es01
```

Inicie o kibana:
```console
docker-compose up -d --build kib01
```

Instale as dependências:
```console
docker-compose run --rm composer install
```

Inicie o horizon:
```console
docker-compose run --rm artisan horizon
```

Rode as migrações:
```console
docker-compose run --rm artisan migrate
```

Rode os seeders:
```console
docker-compose run --rm artisan db:seed --class=PatientSeeder
```

Rode os testes:
```console
docker-compose run --rm artisan test --coverage
```

Rode os testes ignorando os que testam (propositalmente) API externas:
```console
docker-compose run --rm artisan test --exclude-group=api
```


## Descrição

Todos os requisitos e diferenciais foram entregues. Procuro destacar alguns:


### Consulta de CEP com cache

Desenvolvi um API client para o ViaCep implementando interface e injetando no container para facilitar possível troca e poder decorar com o cache. Também normalizei a resposta com um DTO para assegurar o trânsito dos dados independentemente do cliente usado.


### Validação

#### CPF, CNS e CEP

Optei por uma abordagem purista:
* Remover formatação logo na entrada (form requests e importação por arquivo),
* Salvar sempre sem formatação
* (Re)formatar apenas na saída (API resource).

Dessa forma, a API aceita esses campos tanto com máscara quanto sem, e ainda teria o fim de evitar futuros conflitos de dados. Uma alternativa menos purista seria utilizar mutators e accessors.

#### CEP

Optei por não validar o CEP remotamente, pois ainda que durante o fluxo de cadastro a UI possa consultar o endpoint para autocompletar o endereço e assim fazer cache do resultado, entendo que seria em vão, pois seria uma validação muito custosa durante a importação de pacientes e acabaríamos salvando CEPs não verificados de qualquer jeito.

#### CNS

Extraí a lógica de validação de CNS para uma classe de suporte para reaproveitar código tanto para validar quanto para gerar CNS. Como este é o único dado que o faker padrão localizado não gera, criei também um *Person* faker.


#### UF

Geralmente eu faria no mínimo um enum pra validá-lo. Ou lookup table se necessário.


### Importação

Aproveitei o laravel-excel para não reinventar a roda. Mas uma alternativa sem package seria utilizar o recurso de Job Batching do Laravel, com um Job responsável por contar as rows, "chunkiar" e hidratar outros Jobs para evitar timeouts. O laravel-excel acaba fazendo algo muito parecido da forma que foi configurado.

A depender da frequência de uso do recurso de importação, talvez fizesse sentido criar um Model e endpoints para listar imports, facilitando o acompanhamento dos cadastros importados e dos que falharam.

Outra melhoria possível seria salvar em dois bulk inserts, ao invés de linha por linha. Primeiro os pacientes, depois, com o auxílio de um array de row keys e model ids, os endereços.


### Padrão de commits

Para mensagens, procuro sempre lembrar das [7 regras](https://riptutorial.com/git/example/4729/good-commit-messages). Modo imperativo, 50 caracteres no título, documentar o porquê, etc.

Para os branches, utilizei o gitflow, sem ferramenta, marcando merge commits pra visualização de histórico.
