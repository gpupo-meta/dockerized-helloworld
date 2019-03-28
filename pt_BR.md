# Main Quest

Este tutorial/projeto exemplifica como uma aplicação WEB pode rodar em containers, introduz o uso de ferramentas específicas auxiliares para o desenvolvimento de projeto web, mas
**não** trata da [criação e manutenção de imagens Docker](https://opensource.gpupo.com/container-orchestration/), não é necessariamente um roteiro sem viés que busca uma instrução genérica mas sim, bem associada ao meu modo de trabalho, incluindo ferramentas de minha escolha e, apesar de útil para quem procura informações para configurar seu próprio projeto, foca em explicar a organização dos recursos e tecnologias, permitindo que novos contribuidores de projetos que já adotam esta estrutura possam entender, melhorar e executar recursos já configurados.

## Requisitos

Os exemplos abaixo são escritos para execução em um terminal linux, mas você pode facilmente executá-los em outro sistema operacional com alguns ajustes.

Este projeto considera que você já possui o [Docker](https://docs.docker.com/release-notes/docker-ce/) e o [Docker Compose](https://docs.docker.com/compose/install/) instalado em seu sistema operacional(veja [ gpupo-meta/setup-machine](https://github.com/gpupo-meta/setup-machine)). Se você possui um computador sem suporte a virtualização talvéz não consiga rodar o Docker. Eu enfrentei este problema em um Mac Book Pro 2010.

Se você pretende seguir as instruções abaixo até o fim, prepare-se para trafegar mais de 3Gb de dados, entre imagens Docker e pacotes de dependência, então, se você está dependendo de sua conexão EDGE, apenas leia o conteúdo, a leitura recomendada e deixa pra executar pra valer quando estiver melhor de conexão, ok?

Vários termos usados neste tutorial possuem links que facilitarão o entendimento de quem não está familiarizado com eles, então recomendo a leitura das referências.

Alguns comandos devem ser executados em seu terminal tradicional e quando for este o caso, o símbolo :computer: estará presente, porém outros comandos requerem a execução a partir do terminal virtualizado. Quando for este o caso, o símbolo :whale: estará próximo, indicando que a execução deve ser feita no bash do container. Como chegar lá ? Você vai aprender logo abaixo...

Um último requisito importante é **paciência** e **dedicação** pois é bastante coisa pra ler, seguir referências, executar comandos, analizar diffs e refazer até entender. Pra te motivar e também responsabilizar, eu gastei várias horas de trabalho escrevendo este tutorial, tirando as melhores técnicas do meu vaú de tesouross, para que você aí do futuro aprendesse a usá-las, então, me dê algum crédito e esforço quando seguir com este tutorial, ou, se preferir algo mais facil, por [seguir por aqui](http://bfy.tw/Mw0J) ...

Se tudo estiver pronto, selecione seu personagem e vamos em frente.

![Image](https://meta.gpupo.com/dockerized-helloworld/img/start.png)


## Containers e Serviços

:whale: **Dockerized Applications** rodam em [containers](https://www.docker.com/resources/what-container) e possuem um conjunto de serviços (**services**). Seguindo as melhores práticas, para cada responsabilidade é criado (preferencialmente) um serviço.

Exemplificando, em uma solução ([stack](https://en.wikipedia.org/wiki/Solution_stack)) popular como a tradicional [LAMP](https://en.wikipedia.org/wiki/LAMP_%28software_bundle%29), temos as seguintes responsabilidades:

1.  (**L**) Linux, sistema operacional com suporte ao Filesystem, Ferramentas CLI, e  suporte aos softwares instalados;
2.  (**A**) Apache, um webserver instalado e configurado sobre o OS (**L**);
3.  (**M**) *Banco de dados* instalado e configurado sobre o OS (**L**);
4.  (**P**) PHP, interpretador instalado sobre **L**.

Ao convertermos esse tipo de solução, devemos naturalmente pensar em 4 serviços (L, A, M e P). Entretanto, L deixa de ser fundamental pois o suporte ao Filesystem e aos Softwares já existe na dinâmica inerente de uma imagem/container. Então, as Ferramentas CLI ficam sob responsabilidade do serviço **Interpretador** (P).

Até aqui, nosso conjunto de serviços está assim:

1.  **Webserver** - Acessível na porta 80, recebe as requisições, responde com processamento feito pelo serviço interpretador;
2.  **Interpretador** - Acessível somente pelo **Webserver** ou via docker exec, atende a pedidos do **Webserver**, conecta-se ao **Banco de dados**, possui ferramentas CLI;
3.  **Banco de dados**, acessível somente pelo serviço **Interpretador** .

Isso exemplifica uma solução comum, mas vamos nos aprofundar um pouco no nosso modo de trabalho:

Se cada conjunto de serviços possui um **Webserver** que responde na porta 80 da máquina do programador, então somente um projeto pode estar levantado por vez, ou então cada projeto precisa de uma porta exclusiva. Imagine a situação caótica disto em um ambiente de produção. Para resolver isso, cada projeto recebe como parâmetro um subdominio (ex: http://helloworld.localhost) e seu webserver **não** atende em porta pública, mas sim conecta-se ao serviço [httpd-gateway](https://opensource.gpupo.com/httpd-gateway/) que fará o devido roteamento assim que o browser requisitar pelo subdominio configurado.

Também neste ponto, temos uma questão a ser tratada: O serviço do **Banco de dados**. Em um ambiente de desenvolvimento, precisamos de uma base local para testes funcionais, desenvolvimento, testes unitários, etc ... mas no ambiente de produção não precisamos do serviço de banco de dados pois este roda em local diferente da aplicação.

Então possuímos dois conjuntos de serviços: Um para desenvolvimento e outro reduzido para o ambiente de produção.

## Docker Compose File

Esses conjuntos são definidos no arquivo ``docker-compose.yaml``, então em um projeto temos duas versões destas configurações ``Resources/docker-compose.dev.yaml`` e ``Resources/docker-compose.prod.yaml`` e o desenvolvedor faz um link simbólico para a raiz do projeto : :computer:

	ln -sn Resources/docker-compose.dev.yaml ./docker-compose.yaml

### NGINX + PHP-FPM + MariaDB

Ainda no nosso exemplo, baseado na conversão de uma stack LAMP, optamos por utilizar o webserver [NGINX](https://www.nginx.com/) ao invés do Apache e como usamos o PHP como serviço, nossa opção é pelo [PHP-FPM](https://secure.php.net/manual/pt_BR/install.fpm.php). A base de dados é [MariaDB](https://mariadb.org/).

### Ambiente de desenvolvimento X Ambiente de produção

Para complicar um pouco mais, sabemos que no ambiente de produção não é necessário todos os pacotes que o ambiente de desenvolvimento utiliza, então, nosso serviço PHP no conjunto do desenvolvimento possui mais coisas que o mesmo serviço do conjunto de produção.

Para resolver isso, a imagem utilizada pelo serviço **Interpretador**  no conjunto de desenvolvimento é uma extensão da imagem ``PHP-FPM`` com aditivos para o desenvolvedor.

Por exemplo, a extensão ``php-xdebug`` existe na imagem do ambiente de desenvolvimento mas não na imagem usada no ambiente de produção.

Nosso conjunto de serviços ``DEV`` neste momento está assim:

1.  NGINX (**Webserver**) - Acessível via proxy, recebe as requisições, responde com processamento feito pelo serviço interpretador;
2.  PHP-FPM (**Interpretador** )- Acessível somente pelo **Webserver** ou via docker exec, atende a pedidos do **Webserver**, conecta-se ao **Banco de dados**, possui ferramentas CLI;
3.  MariaDB (**Banco de dados**), acessível somente pelo serviço **Interpretador** .

No exemplo de configuração [Resources/docker-compose.dev.yaml](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Resources/docker-compose.dev.yaml) usa-se a imagem gpupo/container-orchestration:symfony-dev para o serviço de nome ``php-fpm``.

A imagem pública [gpupo/container-orchestration:symfony-dev](https://hub.docker.com/r/gpupo/container-orchestration/tags) é uma extensão da imagem oficial ``php-fpm`` sobre debian com a adição de ferramentas necessárias ao desenvolvimento PHP e também de atividades com NodeJS para trabalho com o Webpack.

Para padronizar e facilitar automatização, o serviço do interpretador sempre recebe o nome "**php-fpm**".

Este atual projeto possibilita um [mão na massa](https://en.wikipedia.org/wiki/Hands_on) de acordo com essa explicação.

---

## Rodando a aplicação

**Passo 1**, levantar o [httpd-gateway](https://opensource.gpupo.com/httpd-gateway/): :computer:

	git clone https://github.com/gpupo/httpd-gateway.git;
	pushd httpd-gateway;
	make setup;
	make alone;
	popd;

**Passo 2**, clonar e levantar este projeto: :computer:

	git clone git@github.com:gpupo-meta/dockerized-helloworld.git;
	cd dockerized-helloworld;
	docker-compose up -d;

**Passo 3**, testar o acesso a http://dockerized-helloworld.localhost/helloworld.php ou se preferir, via linha de comando: :computer:

	curl http://dockerized-helloworld.localhost/helloworld.php

Se tudo correu bem até aqui, em
http://dockerized-helloworld.localhost/phpinfo.php você acessa informações sobre o serviço PHP em uso.


**Passo 4**, acesso ao terminal do serviço **Interpretador**: :computer:

	docker-compose exec php-fpm bash

Você verá que ao executar o comando acima é lançado para o ambiente virtualizado onde o diretório atual é ``/var/www/app``.

Se você listar os arquivos do diretório  ``/var/www/app`` verá que são **exatamente** os mesmos da raiz deste projeto.
Isto se dá pelo fato que que [mapeamos o diretório](https://docs.docker.com/compose/compose-file/#volumes) nos parâmetros ``volumes`` existentes nos arquivos __docker-compose*.yaml__

Apesar de você ter instalado em seu sistema operacional, todo um conjunto de interpretadores como por exemplo o PHP, preferenciamente os comandos de manutenção e execução relacionados ao projeto devem ser executados a partir do serviço (container), que possui a versão, configuração e ferramentas escolhidas para o projeto. Após acessar o terminal do serviço **Interpretador**, instale as dependências :whale: :

	make install

Você pode agora chamar o APP CLI deste projeto: :whale:

	bin/dockerized-helloworld

Execução do "Hello World" : :whale:

	bin/dockerized-helloworld greeting "Arnold Schwarzenegger"

## Leitura recomendada

* [Docker Quick Start](https://docs.docker.com/get-started/)

## Perguntas e respostas

**Dúvidas?** Se você precisa de ajuda para entender um dos conceitos acima, [crie uma issue](https://github.com/gpupo-meta/dockerized-helloworld/issues/new),
e marque-a com o **label** ``question``.

![Image](https://meta.gpupo.com/dockerized-helloworld/img/gameover.png)


# Javascript & CSS/ Webpack, SASS, ES2015

A partir deste ponto, a exploração de uma stack tradicional como a LAMP já ficou para trás.
A seguir temos incrementos que lidam com a forma de trabalho usando a imagem [gpupo/container-orchestration:symfony-dev](https://hub.docker.com/r/gpupo/container-orchestration/tags) e outras ferramentas [opensource.gpupo.com](https://opensource.gpupo.com).

### Yarn/NPM/NodeJS

Para a gestão de dependências CSS/Javascript utilizamos o YARN que já está devidamente instalado e configurado na imagem gpupo/container-orchestration:symfony-dev utilizada no service PHP-FPM do Stack de desenvolvimento.

Assim como o comando ``composer install`` instala os pacotes **PHP** definidos em ``compose.json``, o comando ``yarn install`` instala os pacotes **NPM** definidos em ``package.json``: :whale:

	yarn install

Existindo a necessidade de acrescentar um pacote ao projeto, consultamos https://www.npmjs.com/  ou https://yarnpkg.com para encontrar o identificador do pacote. Exemplo: ``babel-plugin-transform-es2015-parameters`` :whale:.

	yarn add babel-plugin-transform-es2015-parameters --dev

O exemplo acima adiciona um pacote que é carregado apenas no ambiente de desenvolvimento já que utilizamos o parâmetro ``--dev``.

#### Compilando (build)

A partir das instruções de ``assets/js/helloworld.js`` será compilado o arquivo ``public/build/helloworld.min.js`` : :whale:

	yarn build

Podemos testar o resultado da seguinte maneira : :whale:

	nodejs public/build/helloworld.min.js

### Babel/ES2015

Uma escrita moderna de código javascript utiliza ``ES6`` também conhecido como ``ECMAScript 6`` ou ``ES2015``.
Aqui entra o [Babel](https://babeljs.io/), um compilador Javascript que nos permite utilizar uma série de recursos ``ES6``.
Não vou detalhar o uso do ``ES6`` aqui nesse documento mas logo abaixo seguem links para o aprendizado da sintaxe.
Em nosso projetto ``dockerized-helloworld`` todas as ferramentas necessárias para compilar javascript ``ES6`` forma instaladas quando você executou ``yarn install``.

O javascript ``assets/js/helloworld-ES2015.js`` foi compilado pelo ``yarn build`` em ``public/build/helloworld-ES2015.min.js``

Podemos testar o resultado da seguinte maneira : :whale:

	nodejs public/build/helloworld-ES2015.min.js

Claro, para que tudo funcionasse foi preciso algumas configurações nos arquivos ``.babelrc`` (instruções para compilação), ``package.json`` (quais pacotes NPM instalar) e ``webpack.config.js`` (quais arquivos compilar e onde fazer o output) e não vou abordar a configuração mas vou deixar links em *leitura recomendada* que tratam disso.

### SASS

O [Sass](https://sass-lang.com/) é uma linguagem baseada em CSS que depois de compilada gera o tradicional CSS.

O arquivo ``assets/scss/app.scss`` inclui todo o css do Bootstrap 4 (disponível na configugação de pacotes e instalados pelo ``yarn install``) e algum código de exemplo que é compilado no path ``public/build/app.min.css``.

### Webpack

A mágica de ``yarn build`` acontece porque o [webpack](https://webpack.js.org/) compila e minimiza nossos arquivos javascript e sass. Mais do que isso, ele recebe a indicação de que o arquivo ``assets/scss/app.scss`` está sendo requerido por ``assets/js/app.js`` e o inclui no processo de build.

![Webpack flow image](https://webpack.github.io/assets/what-is-webpack.png)

Sua configuração é feita a partir do arquivo ``webpack.config.js``.

Você pode acionar o webpack diretamente da seguinte maneira : :whale:

	export PATH="$(yarn bin):$PATH";
	webpack --config webpack.config.js;

Isto é muito útil para testarmos novas configurações.

Para visualizar uma página que carrega o javascript e o css compilado, abra http://dockerized-helloworld.localhost/bootstrap.php .

### Mais leitura recomendada

* [Learn ES2015](https://babeljs.io/docs/en/learn/)
* [Let’s Learn ES2015](https://css-tricks.com/lets-learn-es2015/)
* Google [ES2015](https://developers.google.com/web/shows/ttt/series-2/es2015)
* [O Guia do ES6: TUDO que você precisa saber](https://medium.com/@matheusml/o-guia-do-es6-tudo-que-voc%C3%AA-precisa-saber-8c287876325f)
* [Using Webpack 4 — A “really” quick start](https://medium.com/justfrontendthings/using-webpack-4-a-really-quick-start-under-4-minutes-61ff3fa9a2c8)
* [How to include Bootstrap in your project with Webpack](https://stevenwestmoreland.com/2018/01/how-to-include-bootstrap-in-your-project-with-webpack.html)
* [Webpack 4: Extract CSS from Javascript files with mini-css-extract-plugin](https://quantizd.com/webpack-4-extract-css-with-mini-css-extract-plugin/)
* [CSS menos sofrido com Sass](https://blog.caelum.com.br/css-menos-sofrido-com-sass/)
* [Sass Basics](https://sass-lang.com/guide)
* [Webpack manual](https://webpack.js.org/concepts)

# Extra services & Tools

A partir deste momento vamos incluir novos ``services`` em nosso projeto e para isso vamos deixar de usar o ``docker-compose file`` atual e passaremos a usar o arquivo ``Resources/docker-compose.extra-services.yaml``.

Para isso, vamos aos passos de configuração:

**Passo 1**, derrube os serviços atuais : :computer:

	docker-compose down

**Passo 2**, substitua o [link simbólico](https://www.shellhacks.com/symlink-create-symbolic-link-linux/)  de ``docker-compose.yaml`` (que atualmente aponta para ``Resources/docker-compose.dev.yaml``) para ``Resources/docker-compose.extra-services.yaml`` : :computer:

	ln -snf Resources/docker-compose.extra-services.yaml ./docker-compose.yaml

**Passo 3**, levante os Serviços : :computer:

	docker-compose up -d

### PhpMyAdmin (extra)

Agora, no subdomínio [phpmyadmin-dockerized-helloworld.localhost](http://phpmyadmin-dockerized-helloworld.localhost) você poderá acessar o [PhpMyAdmin](https://www.phpmyadmin.net/)

No arquivo [Resources/docker-compose.dev.yaml](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Resources/docker-compose.dev.yaml) eu incluí o serviço que oferece o **PhpMyAdmin**, usando a imagem Docker oficial.

### Redis

O [Redis](https://aws.amazon.com/pt/elasticache/what-is-redis/) é um armazenamento de estrutura de dados de chave-valor de código aberto e na memória e usamos frequentemente em aplicações PHP para substituir o [Cache APC](https://www.php.net/manual/en/book.apc.php).

## Logstash

### Persistência de logs na stack RELK

Tradicionalmente, uma aplicação grava logs em um arquivo como por exemplo, uma aplicação [Symfony 4](https://symfony.com/) gravará seus logs em ``var/logs/dev.log``, ``var/logs/prod.log`` ou ``var/logs/dev.log``, mas nós que estamos projetando uma aplicação que roda em containers precisamos de uma forma melhor de armazenar estes registros pois, um dos fundamentos do uso de containers é que cada container é projetado para atender um processamento por um tempo determinado e **é descartável**. Não podemos perder os logs da aplicação a cada vez que recriamos um container ou mudamos a imagem na qual ele é baseado. Poderíamos resolver este problema com a técnica de mapeamento de um diretório da máquina host para ``var/logs`` mas isto continua centralizando os logs em uma máquina e em uma arquitetura de nuvem, usamos muitas máquinas, então teríamos que abrir muitos diretórios para buscar uma informação e a partir de certa quantidade de máquinas, fazer isto se torna inviável. Então, uma engenharia que resolve de maneira muito habilidosa este problema é enviar todos os logs para um [servidor de logs](https://en.wikipedia.org/wiki/Server_log). Simples assim. :boom:

Para montar esse servidor usamos basicamente 4 ``services``:

![RELK flow image](https://meta.gpupo.com/dockerized-helloworld/img/relk.jpg)

1.  (**R**) RabbitMQ;
2.  (**E**) Elasticsearch;
3.  (**L**) Logstash;
4.  (**K**) Kibana.

Nossa aplicação, utilizando um drive específico ([php-amqplib/php-amqplib](https://github.com/php-amqplib/php-amqplib)), envia os logs para um servidor [RabbitMQ](https://www.rabbitmq.com/) que os guarda em uma fila.

O [Logstash](https://www.elastic.co/products/logstash) conecta no **RabbitMQ** e coleta os registros de log, (transforma-os se necessário) e grava-os no [Elasticsearch](https://www.elastic.co/). O [Kibana](https://www.elastic.co/products/kibana) é uma interface de leitura e exploração destes logs que estão gravados no **Elasticsearch**.

#### Levantando a stack

Para nossa :whale: **Dockerized Application** não é interessante responsabilizar-se pela configuração da **stack RELK**, então eu preparei este conjunto de serviços em um lugar secreto, que você simplesmente vai levantar com o seguinte setup: :computer:


	make relk@up

Logstash config: :whale:

```bash
curl -XPOST -D- 'http://kibana:5601/api/saved_objects/index-pattern' \
	-H 'Content-Type: application/json' \
	-H 'kbn-version: 6.2.4' \
	-d '{"attributes":{"title":"logstash-*","timeFieldName":"@timestamp"}}'
```

Agora vamos testar o envio de logs: :whale:

	bin/dockerized-helloworld log:generator 100

##### Acesso aos dashboards dos serviços da stack RELK

Se tudo correu bem você terá acesso aos serviços:

* [RabbitMQ](http://dockerized-helloworld.localhost:15672/), user ``admin``, password ``d0ck3r1zzd``
* [Kibana](http://dockerized-helloworld.localhost:5601)
* [Logstash API](http://dockerized-helloworld.localhost:9600/_node/hot_threads?human=true)

Para o nosso tutorial, o mais importante é que você consiga visualizar os logs gerados pela aplicação e para isso deverá acessar o Kibana e escolher no menu o item **Discover**. Você deverá ver uma tela semelhante a essa:

![Kibana dashboard image](https://meta.gpupo.com/dockerized-helloworld/img/kibana.png)

:memo: Essa mesma lógica de envio de logs para um local centralizado pode ser adotada por qualquer software, não somente Apps PHP. O [httpd-gateway](https://opensource.gpupo.com/httpd-gateway/) está preparado para também enviar os logs do NGINX para um servidor de logs, em um ambiente de produção.

#### Algumas dicas sobre logs

1. Muita informação é ruído e pouca informação é inadequado. É difícil encontrar o equilíbrio do ideal, mas esse é o desafio. No caso de microsserviços, pense também na rastreabilidade entre serviços, como o uso de um identificador do ``service``. Outra coisa a ter em mente é que os logs são temporais, não permanentes, com vida útil de alguns meses.
2. Siga [severity levels](https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md#log-levels) (Syslog Protocol).
3. Estruture seus logs. Siga um padrão JSON acordado para o registro. Isso facilita a análise e a pesquisa.
4. Grave os registros com cuidado sem não prejudicar o desempenho.
5. Considere que o servidor de log pode estar indisponível e sua aplicação precisa resistir a isso.
6. Em uma aplicação PHP sólida, utilize o [Monolog](https://github.com/Seldaek/monolog/). Veja [Symfony Guide Logging](https://symfony.com/doc/current/logging.html).

#### Leitura Recomendada

* [Tutorials for using RabbitMQ in various ways](http://www.rabbitmq.com/getstarted.html)
* [Tutorial RabbitMQ X PHP](https://www.rabbitmq.com/tutorials/tutorial-one-php.html)

#### Outras persistências

De fato você já está acostumado a persistir fora da aplicação informações como os dados no banco relacional.
Um artefato importante a ser persistido externamente são arquivos estáticos enviados por usuários a partir de formulários de upload por exemplo.
Para atender esta demanda eu uso o projeto [Content Butler](https://github.com/gpupo/content-butler) associado ao [Doctrine PHP Content Repository ODM](https://www.doctrine-project.org/projects/doctrine-phpcr-odm/en/latest/index.html) que trata estes assets como objetos e os gerencia em um servidor [Apache Jackrabbit](https://jackrabbit.apache.org/jcr/index.html).

---

### Make

[Make](https://en.wikipedia.org/wiki/Make_%28software%29) é uma ferramenta para automatização de build criada em 1976 e desenhada para resolver problemas durante o processo de build, originalmente usada em projetos de [linguagem C](https://en.wikipedia.org/wiki/C_%28programming_language%29) e que passou a ser amplamente utilizada em projetos [Unix Like](https://en.wikipedia.org/wiki/Unix-like).

Seu arquivo de configuração é o [Makefile](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Makefile) que está na raiz deste projeto. É nesse arquivo que configuramos ``targets``. Cada target é uma sequencia de instruções, que pode por sua vez depender de outros targets.

A sintaxe de um target é:

```make
## Coment
target: [prerequisite]
    command1
    [command2]
```

Devido à configuração customizada de nosso [Makefile](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Makefile), se você simplesmente executar ``make`` sem especificar qual target quer acionar, uma lista de targets e suas descrições será exibida. Experimente : :whale:

	make

![Make output](https://meta.gpupo.com/dockerized-helloworld/img/make.png)

Na verdade, logo no começo deste tutorial eu pedi para que você executace ``make install``. Isto fez com que fosse acionado o target **install** configurado no Makefile:

```make
## Composer Install
install:
	composer self-update
	composer install --prefer-dist
```
O target **install** segue o script de atualizar o [Composer](https://getcomposer.org/) e instalar as dependências PHP. Se o objetivo deste target fosse de instalar tudo o que o projeto precisa, o que faz sentido em um target destes em um projeto real, poderíamos acrescentar a chamada para instalação dos pacotes NPM e ainda a necessidade de realizar o build após instalação:

```make
## Instala as dependências o que o projeto precisa
install:
	composer self-update
	composer install --prefer-dist
	yarn install
	yarn build
```

Experimente o target ``bash`` que vai lhe lançar diretamente no bash do serviço ``PHP-FPM``:

	make bash

## QA Tools

A imagem [gpupo/container-orchestration:symfony-dev](https://hub.docker.com/r/gpupo/container-orchestration/tags) possui ferramentas de [quality assurance](https://en.wikipedia.org/wiki/Software_quality_assurance) que nos ajudam a manter a qualidade da escrita e da engenharia.

### Coding Standard

Neste projeto seguimos [PHP Standards Recommendations](https://www.php-fig.org/psr/)(PSR) e também padrões sugeridos pelo projeto [Symfony](https://symfony.com/) com objetivo facilitar a reutilização de código entre os diversos projetos que implementem determinado padrão.

Se você ainda não está familiarizado com as PSRs, saiba que existem PSRs para implementações de [autoload](http://br1.php.net/manual/en/function.autoload.php)[ (](http://www.php-fig.org/psr/psr-4/)[PSR-4](http://www.php-fig.org/psr/psr-4/)), sugestões de estilos de código, como posição de chaves, indentação ([Usar tabulações ou espaços?](http://www.jwz.org/doc/tabs-vs-spaces.html)) ([PSR-1](http://www.php-fig.org/psr/psr-1/) e [PSR-2](http://www.php-fig.org/psr/psr-2/)).

Existem também propostas em draft para padronização dos docblock de documentação ([PSR-5](https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md)) e uma interface para requisições HTTP ([PSR-7](https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md))

Mais informações leia o [FAQ](https://www.php-fig.org/faqs/) e visite o [repositório no GitHub](https://github.com/php-fig/fig-standards) com os padrões já aceitos.

#### Principais padrões de escrita adotados neste projeto

*   [PSR-1: Basic Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
*   [PSR-2: Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
*   [PSR-4: Autoloading Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
*   [PSR-5: PHPDoc (draft)](https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md)
*   [Symfony Coding Standards](https://symfony.com/doc/current/contributing/code/standards.html)

#### php-cs-fixer

Uma ferramenta muito importante é o [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) que vai alinhar o código escrito de acordo com as regras de padrão selecionados para o projeto.

No arquivo [.php_cs.dist](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/.php_cs.dist) é configurado este conjunto de regras.

Vamos a um exemplo prático! Apesar de funcionar, o arquivo [src/Traits/VeryWrongCodeStyleTrait.php](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/src/Traits/VeryWrongCodeStyleTrait.php) está mal escrito e ignora vários padrões de escrita. Mas que padrões são estes?
Rode o [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer): :whale:

	make php-cs-fixer

Se você executar um ``git diff`` verá algo assim:


```diff
<?php

+declare(strict_types=1);
+
+/*
+ * This file is part of gpupo/dockerized-helloworld
+ * Created by Gilmar Pupo <contact@gpupo.com>
+ * For the information of copyright and license....
+ *
+ */
+
 namespace Gpupo\DockerizedHelloworld\Traits;

-use JMS\Serializer\Annotation as JMS,
-    Doctrine\ODM\MongoDB\Mapping\Annotations as ODM,
-    PDO;
+use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
+use JMS\Serializer\Annotation as JMS;

 /**
- * Very wrong code style
- *
- *
- *
+ * Very wrong code style.
  */
-trait VeryWrongCodeStyleTrait {
-
+trait VeryWrongCodeStyleTrait
+{
     /**
      * @var string
      * @ODM\Field(type="string")
     private $name;

     /**
-     * Set name
+     * Set name.
+     *
+     * @param string $name
      *
-     * @param  string $name
      * @return mixed
      */

```

Nesse diff que o arquivo recebeu modificações:

* Adicionou a declaração ``declare(strict_types=1);``.
* Adicionou o *HEADER* padrão a todos os arquivos PHP do projeto.
* Organizou em ordem alfabética as declarações de uso.
* Escreveu com um ``use`` por linha, como pede o CS configurado.
* Removeu o ``use PDO`` pois a classe PDO não recebe nenhum uso nas linhas do arquivo.
* Trocou as ``{`` de lugar, de acordo com o codding style definido.
* Adicionou ponto final a linhas de documentação.

:memo: É uma boa prática você utilizar o ``make php-cs-fixer`` após terminar o desenvolvimento de uma feature PHP.

#### PHP_CodeSniffer

Outra ferramenta importante é o [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) que também irá nos ajudar a manter um padrão acordado e é configurado no arquivo [phpcs.xml.dist](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/phpcs.xml.dist)

Experimente: :whale:

	make phpcbf

...

### PHPMD

O [PHPMD](https://phpmd.org/) - Ruleset for PHP Mess Detector that enforces coding standards é configurado no arquivo [.phpmd.xml](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/.phpmd.xml).

	make phpmd

### Phan


---

### Testes unitários

O Makefile está configurado para rodar os testes Unitários: :whale:

	make phpunit

Você verá algo semelhante a essa saída:

![Image](https://meta.gpupo.com/dockerized-helloworld/img/phpunit.png)


#### Esqueletos automáticos

Nosso projeto usa a [gpupo/common](https://opensource.gpupo.com/common/) que contém o comando ``vendor/bin/developer-toolbox`` que pode nos ajudar a criar um teste unitário esqueleto a partir de uma classe existente.

Se quisermos criar um teste unitário para o objeto ``Gpupo\DockerizedHelloworld\Entity\Person``: :whale:

	vendor/bin/developer-toolbox generate --class='Gpupo\DockerizedHelloworld\Entity\Person'

O comando acima gerará o arquivo ``tests/Entity/PersonTest.php`` que deve conter um conteúdo semelhante a este:


```PHP
<?php
//...
namespace Gpupo\DockerizedHelloworld\Tests\Entity;

use PHPUnit\Framework\TestCase as CoreTestCase;
use Gpupo\DockerizedHelloworld\Entity\Person;

/**
 * @coversDefaultClass \Gpupo\DockerizedHelloworld\Entity\Person
 * ...
 */
class PersonTest extends CoreTestCase
{
    public function dataProviderPerson()
    {
        $expected = [
            "name" => "d1b72da",
        ];
        $object = new Person();

        return [[$object, $expected]];
    }

    /**
     * @testdox Have a getter getName() to get Name
     * @dataProvider dataProviderPerson
     * @cover ::getName
     * @small
     * @test
     *
     * @param Person $person Main Object
     * @param array $expected Fixture data
     */
    public function testGetName(Person $person, array $expected)
    {
        $person->setName($expected['name']);
        $this->assertSame($expected['name'], $person->getName());
    }

    /**
     * @testdox Have a setter setName() to set Name
     * @dataProvider dataProviderPerson
     * @cover ::setName
     * @small
     * @test
     *
     * @param Person $person Main Object
     * @param array $expected Fixture data
     */
    public function testSetName(Person $person, array $expected)
    {
        $person->setName($expected['name']);
        $this->assertSame($expected['name'], $person->getName());
    }
}
```


Execute os testes Unitários: :whale:

	make phpunit

Lembre-se de que o arquivo ``tests/Entity/PersonTest.php`` é um rascunho inicial
 e você precisa continuar seu desenvolvimento para transformá-lo em um teste de qualidade.

Quando você tentar editar o arquivo ``tests/Entity/PersonTest.php`` em seu IDE, não conseguirá gravar suas alterações, o que nos leva para a próxima fase ...

![Image](https://meta.gpupo.com/dockerized-helloworld/img/permission.png)


#### O problema de permissões

Os arquivos gerados a partir da [shell session](https://superuser.com/questions/651111/what-is-the-definition-of-a-session-in-linux) do :whale: container não possuem o mesmo dono que os arquivos gerados na session da :computer: máquina host. Isto porque os linux users são diferentes em cada session. Em um caso muito comum, o arquivo gerado pela session do container pertencerá ao root do container e também ao root do host, e seu usuário atual, na máquina do host não poderá editá-lo. Existem várias formas de contornar isso. Serei agressivo, na escolha do contorno, dizendo ao projeto "dê-me tudo isso aqui, pois é meu!" com sudo + chown: :computer:

	sudo chown -R $USER:$USER ./

....

---

# Considerações finais

![Image](https://meta.gpupo.com/dockerized-helloworld/img/congrats.jpg)


Muito bem, você zerou o jogo :) :checkered_flag:

Você pode contribuir com este projeto criando uma [Pull Request](https://help.github.com/en/articles/creating-a-pull-request) ou informando o bug/melhoria em [issues](https://github.com/gpupo-meta/dockerized-helloworld/issues). Isto inclui correções ortográficas.

Veja a [lista de melhorias](https://github.com/gpupo-meta/dockerized-helloworld/labels/enhancement) que precisam de desenvolvimento.

### Shutdown

Para desligar todos os container levantados durante a execução deste tutorial, você pode usar este comando: :computer:

	docker stop $(docker ps -a -q)

:memo: Agora um extra, se você configurou seu computador usando o [ gpupo-meta/setup-machine](https://github.com/gpupo-meta/setup-machine), basta executar:  :computer:

	docker-stop-all

Depois de carregar milhões de bits em imagens Docker, talvez você precise liberar removendo todas as imagens Docker em cache: :computer:

	docker-remove-all


![Cya image](https://meta.gpupo.com/dockerized-helloworld/img/pizzatime.jpg)

