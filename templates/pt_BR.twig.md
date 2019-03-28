# Main Quest

{% include 'templates/pt_BR/intro.twig.md' %}

## Requisitos

{% include 'templates/pt_BR/requirements.twig.md' %}

![Start](https://meta.gpupo.com/dockerized-helloworld/img/start.png)

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

![Game over image](https://meta.gpupo.com/dockerized-helloworld/img/gameover.png)

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

...

### PHPMD

O [PHPMD](https://phpmd.org/) - Ruleset for PHP Mess Detector that enforces coding standards é configurado no arquivo [.phpmd.xml](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/.phpmd.xml).

	make phpmd

### Phan


---

### Testes unitários

O Makefile está configurado para rodar os testes Unitários


#### Esqueletos automáticos

{% include 'templates/pt_BR/developer-toolbox-generate.twig.md' %}

![Permission problem image](https://meta.gpupo.com/dockerized-helloworld/img/permission.png)

#### O problema de permissões

....

---

# Considerações finais

![Congratulations image](https://meta.gpupo.com/dockerized-helloworld/img/congrats.jpg)

{% include 'templates/pt_BR/final-considerations.twig.md' %}

---
{:toc}

![Cya image](https://meta.gpupo.com/dockerized-helloworld/img/pizzatime.jpg)
