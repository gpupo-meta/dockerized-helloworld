# Main Quest

Este projeto exemplifica como uma aplicação WEB pode rodar em containers, introduz o uso de ferramentas específicas auxiliares mas
**não** trata da [criação e manutenção de imagens Docker](https://opensource.gpupo.com/container-orchestration/).

## Requisitos

Os exemplos abaixo são escritos para execução em um terminal linux, mas você pode facilmente executá-los em outro sistema operacional com alguns ajustes.

Este projeto considera que você já possui o [Docker](https://docs.docker.com/release-notes/docker-ce/) e o [Docker Compose](https://docs.docker.com/compose/install/) instalado em seu sistema operacional(veja [ gpupo-meta/setup-machine](https://github.com/gpupo-meta/setup-machine)).

## Containers e Serviços

Dockerized Applications rodam em [containers](https://www.docker.com/resources/what-container) e possuem um conjunto de serviços. Seguindo as melhores práticas, para cada responsabilidade é criado (preferencialmente) um serviço.

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

Esses conjuntos são definidos no arquivo ``docker-compose.yaml``, então em um projeto temos duas versões destas configurações ``Resources/docker-compose.dev.yaml`` e ``Resources/docker-compose.prod.yaml`` e o desenvolvedor faz um link simbólico para a raiz do projeto:

	ln -sn Resources/docker-compose.dev.yaml ./docker-compose.yaml

## NGINX + PHP-FPM + MariaDB

Ainda no nosso exemplo, baseado na conversão de uma stack LAMP, optamos por utilizar o webserver [NGINX](https://www.nginx.com/) ao invés do Apache e como usamos o PHP como serviço, nossa opção é pelo [PHP-FPM](https://secure.php.net/manual/pt_BR/install.fpm.php). A base de dados é [MariaDB](https://mariadb.org/).

## Ambiente de desenvolvimento X Ambiente de produção

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

## Setup


**Passo 1**, levantar o [httpd-gateway](https://opensource.gpupo.com/httpd-gateway/):

	git clone https://github.com/gpupo/httpd-gateway.git;
	pushd httpd-gateway;
	make setup;
	make alone;
	popd;

**Passo 2**, clonar e levantar este projeto:

	git clone git@github.com:gpupo-meta/dockerized-helloworld.git;
	cd dockerized-helloworld;
	docker-compose up -d;

**Passo 3**, testar o acesso a http://dockerized-helloworld.localhost/helloworld.php ou se preferir, via linha de comando:

	curl http://dockerized-helloworld.localhost/helloworld.php

## Execução

Se tudo correu bem até aqui, em
http://dockerized-helloworld.localhost/phpinfo.php você acessa informações sobre o serviço PHP em uso.

## PhpMyAdmin (extra)

Em http://phpmyadmin-dockerized-helloworld.localhost você poderá acessar o [PhpMyAdmin](https://www.phpmyadmin.net/)

No arquivo [Resources/docker-compose.dev.yaml](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Resources/docker-compose.dev.yaml) eu incluí o serviço que oferece o PhpMyAdmin, usando a imagem Docker oficial.

**Passo 4**, acesso ao terminal do serviço **Interpretador**:

	docker-compose exec php-fpm bash

Você verá que ao executar o comando acima é lançado para o ambiente virtualizado onde o diretório atual é ``/var/www/app``.

Se você listar os arquivos do diretório  ``/var/www/app`` verá que são **exatamente** os mesmos da raiz deste projeto.
Isto se dá pelo fato que que [mapeamos o diretório](https://docs.docker.com/compose/compose-file/#volumes) nos parâmetros ``volumes`` existentes nos arquivos __docker-compose*.yaml__


Apesar de você ter instalado em seu sistema operacional, todo um conjunto de interpretadores como por exemplo o PHP, preferenciamente os comandos de manutenção e execução relacionados ao projeto devem ser executados a partir do serviço (container), que possui a versão, configuração e ferramentas escolhidas para o projeto. Após acessar o terminal do serviço **Interpretador**, instale as dependências:

	make install


Você pode agora chamar o APP CLI deste projeto:

	bin/dockerized-helloworld

Execução do "Hello World":

	bin/dockerized-helloworld greeting "Arnold Schwarzenegger"

---

## Leitura recomendada

* [Docker Quick Start](https://docs.docker.com/get-started/)

---

## Perguntas e respostas

**Dúvidas?** Se você precisa de ajuda para entender um dos conceitos acima, [crie uma issue](https://github.com/gpupo-meta/dockerized-helloworld/issues/new),
e marque-a com o **label** ``question``.

---

## Contribuição

Você pode contribuir com este projeto criando uma [Pull Request](https://help.github.com/en/articles/creating-a-pull-request) ou informando o bug/melhoria em [issues](https://github.com/gpupo-meta/dockerized-helloworld/issues).

---

# Side Quests

A partir deste ponto, a exploração de uma stack tradicional como a LAMP já ficou para trás.
A seguir temos incrementos que lidam com a forma de trabalho usando a imagem [gpupo/container-orchestration:symfony-dev](https://hub.docker.com/r/gpupo/container-orchestration/tags) e outras ferramentas [opensource.gpupo.com](https://opensource.gpupo.com).

## NPM, Yarn, Babel e Webpack

### Yarn

Para a gestão de dependências CSS/Javascript utilizamos o YARN que já está devidamente instalado e configurado na imagem gpupo/container-orchestration:symfony-dev utilizada no service PHP-FPM do Stack de desenvolvimento.

Assim como o comando ``composer install`` instala os pacotes **PHP** definidos em ``compose.json``, o comando ``yarn install`` instala os pacotes **NPM** definidos em ``package.json``.

	yarn install

Existindo a necessidade de acrescentar um pacote ao projeto, consultamos https://www.npmjs.com/  ou https://yarnpkg.com para encontrar o identificador do pacote. Exemplo: ``babel-plugin-transform-es2015-parameters``.

	yarn add babel-plugin-transform-es2015-parameters --dev

O exemplo acima adiciona um pacote que é carregado apenas no ambiente de desenvolvimento já que utilizamos o parâmetro ``--dev``.

### Compilando

A partir das instruções de ``assets/js/helloworld.js`` será compilado o arquivo ``public/build/helloworld.min.js``

	yarn build

Podemos testar o resultado da seguinte maneira:

	nodejs public/build/helloworld.min.js

### Babel

Uma escrita moderna de código javascript utiliza ``ES6`` também conhecido como ``ECMAScript 6`` ou ``ES2015``.
Aqui entra o [Babel](https://babeljs.io/), um compilador Javascript que nos permite utilizar uma série de recursos ``ES6``.
Não vou detalhar o uso do ``ES6`` aqui nesse documento mas logo abaixo seguem links para o aprendizado da sintaxe.
Em nosso projetto ``dockerized-helloworld`` todas as ferramentas necessárias para compilar javascript ``ES6`` forma instaladas quando você executou ``yarn install``.

O javascript ``assets/js/helloworld-ES2015.js`` foi compilado pelo ``yarn build`` em ``public/build/helloworld-ES2015.min.js``

Podemos testar o resultado da seguinte maneira:

	nodejs public/build/helloworld-ES2015.min.js

Claro, para que tudo funcionasse foi preciso algumas configurações nos arquivos ``.babelrc`` (instruções para compilação), ``package.json`` (quais pacotes NPM instalar) e ``webpack.config.js`` (quais arquivos compilar e onde fazer o output).

### SASS

O [Sass](https://sass-lang.com/) é uma linguagem baseada em CSS que depois de compilada gera o tradicional CSS.

O arquivo ``assets/scss/app.scss`` inclui todo o css do Bootstrap 4 (disponível na configugação de pacotes e instalados pelo ``yarn install``) e algum código de exemplo que é compilado no path ``public/build/app.min.css``.

### Webpack

A mágica de ``yarn build`` acontece porque o [webpack](https://webpack.js.org/) compila e minimiza nossos arquivos javascript e sass. Mais do que isso, ele recebe a indicação de que o arquivo ``assets/scss/app.scss`` está sendo requerido por ``assets/js/app.js`` e o inclui no processo de build.

![Webpack flow image](https://webpack.github.io/assets/what-is-webpack.png)

Sua configuração é feita a partir do arquivo ``webpack.config.js``.

Você pode acionar o webpack diretamente da seguinte maneira:

	export PATH="$(yarn bin):$PATH";
	webpack --config webpack.config.js;

Isto é muito útil para testarmos novas configurações.

Para visualizar uma página que carrega o javascript e o css compilado, abra http://dockerized-helloworld.localhost/bootstrap.php .


### Leitura recomendada

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

## RELK

RabbitMq / ElasticSerach  Logstash / Kibana  (RELK)


...

## Redis

...

___

# Todo list

...
