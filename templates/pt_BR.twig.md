{% extends "templates/base.twig.md" %}
{% import 'templates/includes/macros.twig' as macros %}

{% block main_quest %}

{% include 'templates/pt_BR/intro.twig.md' %}

## Requisitos

{% include 'templates/pt_BR/requirements.twig.md' %}

{{ macros.add_img('start.png') }}

## Containers e Serviços

{% include 'templates/pt_BR/containers-services.twig.md' %}

## Docker Compose File

Esses conjuntos são definidos no arquivo ``docker-compose.yaml``, então em um projeto temos duas versões destas configurações ``Resources/docker-compose.dev.yaml`` e ``Resources/docker-compose.prod.yaml`` e o desenvolvedor faz um link simbólico para a raiz do projeto : :computer:

	ln -sn Resources/docker-compose.dev.yaml ./docker-compose.yaml

### NGINX + PHP-FPM + MariaDB

Ainda no nosso exemplo, baseado na conversão de uma stack LAMP, optamos por utilizar o webserver [NGINX](https://www.nginx.com/) ao invés do Apache e como usamos o PHP como serviço, nossa opção é pelo [PHP-FPM](https://secure.php.net/manual/pt_BR/install.fpm.php). A base de dados é [MariaDB](https://mariadb.org/).

### Ambiente de desenvolvimento X Ambiente de produção

{% include 'templates/pt_BR/environment.twig.md' %}

Este atual projeto possibilita um [mão na massa](https://en.wikipedia.org/wiki/Hands_on) de acordo com essa explicação.

---

## Rodando a aplicação

{% include 'templates/pt_BR/run.twig.md' %}

## Leitura recomendada

* [Docker Quick Start](https://docs.docker.com/get-started/)

## Perguntas e respostas

**Dúvidas?** Se você precisa de ajuda para entender um dos conceitos acima, [crie uma issue](https://github.com/gpupo-meta/dockerized-helloworld/issues/new),
e marque-a com o **label** ``question``.

{{ macros.add_img('gameover.png') }}

# Javascript & CSS/ Webpack, SASS, ES2015

{% include 'templates/pt_BR/js-css-webpack-sass.twig.md' %}

# Extra services & Tools

{% include 'templates/pt_BR/extra-services-setup.twig.md' %}

### PhpMyAdmin (extra)

Agora, no subdomínio [phpmyadmin-dockerized-helloworld.localhost](http://phpmyadmin-dockerized-helloworld.localhost) você poderá acessar o [PhpMyAdmin](https://www.phpmyadmin.net/)

No arquivo [Resources/docker-compose.dev.yaml](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Resources/docker-compose.dev.yaml) eu incluí o serviço que oferece o **PhpMyAdmin**, usando a imagem Docker oficial.

### Redis

O [Redis](https://aws.amazon.com/pt/elasticache/what-is-redis/) é um armazenamento de estrutura de dados de chave-valor de código aberto e na memória e usamos frequentemente em aplicações PHP para substituir o [Cache APC](https://www.php.net/manual/en/book.apc.php).

## Logstash

{% include 'templates/pt_BR/relk.twig.md' %}

---

### Make

{% include 'templates/pt_BR/make.twig.md' %}

## QA Tools

A imagem [gpupo/container-orchestration:symfony-dev](https://hub.docker.com/r/gpupo/container-orchestration/tags) possui ferramentas de [quality assurance](https://en.wikipedia.org/wiki/Software_quality_assurance) que nos ajudam a manter a qualidade da escrita e da engenharia.

### Coding Standard

{% include 'templates/pt_BR/coding-standard.twig.md' %}

#### php-cs-fixer

{% include 'templates/pt_BR/php-cs-fixer.twig.md' %}

#### PHP_CodeSniffer

Outra ferramenta importante é o [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) que também irá nos ajudar a manter um padrão acordado e é configurado no arquivo [phpcs.xml.dist](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/phpcs.xml.dist)

Experimente: :whale:

	make phpcbf

As ferramentas php-cs-fixer, PHP_CodeSniffer complementam-se e uma pode apontar uma melhoria que outra não pegou. Com o ``make`` podemos criar um ``target`` se seja ajunte uma coleção de outros ``targets``. Em nosso Makefile, o target **cs** se presta isso: :whale:

	make cs

### PHPMD

O [PHPMD](https://phpmd.org/) - Ruleset for PHP Mess Detector that enforces coding standards é configurado no arquivo [.phpmd.xml](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/.phpmd.xml). :whale:

	make phpmd

### Phan

[Phan](https://github.com/phan/phan), static analyzer para o PHP. Está configurado no arquivo [config/phan.php ](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/config/phan.php) e vai nos apontar melhorias e possíveis erros da aplicação: :whale:

	make phan

### PHPSTAN

	make phpstan

### PHPLOC

	make phploc

---

### Testes unitários

O Makefile está configurado para rodar os testes Unitários: :whale:

	make phpunit

Você verá algo semelhante a essa saída:

{{ macros.add_img('phpunit.png') }}

o diretório ``tests/`` guarda nos testes unitários que são executados pelo [phpunit](https://phpunit.de/). Apesar de apenas existir o teste [tests/Console/Command/GreetingCommandTest.php](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/tests/Console/Command/GreetingCommandTest.php) que valida a saudação emitida pelo comando de console ``bin/dockerized-helloworld greeting `` executado anteriormente, algumas técnicas interessantes são utilizadas, como a validação repetida usando [dataproviders](https://phpunit.readthedocs.io/en/8.0/writing-tests-for-phpunit.html#data-providers) e o teste do output de um [Command](https://symfony.com/doc/current/console.html).

 Vamos para algo mais simples ...

#### Esqueletos automáticos

{% include 'templates/pt_BR/developer-toolbox-generate.twig.md' %}

{{ macros.add_img('permission.png') }}

#### O problema de permissões

Os arquivos gerados a partir da [shell session](https://superuser.com/questions/651111/what-is-the-definition-of-a-session-in-linux) do :whale: container não possuem o mesmo dono que os arquivos gerados na session da :computer: máquina host. Isto porque os linux users são diferentes em cada session. Em um caso muito comum, o arquivo gerado pela session do container pertencerá ao root do container e também ao root do host, e seu usuário atual, na máquina do host não poderá editá-lo. Existem várias formas de contornar isso. Serei agressivo, na escolha do contorno, dizendo ao projeto "dê-me tudo isso aqui, pois é meu!" com sudo + chown: :computer:

	sudo chown -R $USER:$USER ./

....

---

# Considerações finais

{{ macros.add_img('congrats.jpg') }}

{% include 'templates/pt_BR/final-considerations.twig.md' %}

{% endblock %}
