---
layout: default
permalink: /en.html
---

* [Versão em Portugues](https://meta.gpupo.com/dockerized-helloworld/pt_BR.html)
* [English Version](https://meta.gpupo.com/dockerized-helloworld/en.html)

# Main Quest


This tutorial/project exemplify how a WEB application can run into containers, introduce the use of especifics auxialiary tools for web project development, but ***do not*** deals with [creation and manuntence Docker's image](https://opensource.gpupo.com/container-orchestration/), it is not necessarily a guide wihtout bias that search a generic instruction but, well associated to my work mode, including my onw choose tools and, although be helpful for those who serch information to configure their own projects, focuses in explain the resources and tecnologies, allowing that new projects contributors that already adopts this structure may understand, improve and execute resources already configureted.

## Requirements

The examples below are written for execution on linux terminal, but you can easily execute them in others operating system with some adjusts.

This project considers that you already have [Docker](https://docs.docker.com/release-notes/docker-ce/) and [Docker Compose](https://docs.docker.com/compose/install/) installed in your operating system (see [ gpupo-meta/setup-machine](https://github.com/gpupo-meta/setup-machine)). If you have a computer without virtualization suport maybe you can not run Docker. I faced this problem on my Mac Book Pro 2010.

If you aims follow the instructions below to the end, prepare yourself to trafegate more tham 3Gb of data beteewn Docker images and dependecies packages, so, if you depends on you EDGE connection, just read the contents, the recommended reading and let it execute when you got a better connection, ok?

Many terms useds in this tutorial have links that will facilitate the understanding of who is not familiarized with them, so I recomend read the references.

Some commands should be executed in traditional terminal and when it is the case, the simbol :computer: will be present, however others commands require an execution from a virtualizated terminal. Whem it is the case the simbol :whale: will be close, pointing that the execution shoud be done on container's bash. How get there? You will learn right below...

One last important requirement is ***patience*** and ***dedication*** because it is a lot of things to read, follow references, execute commands, analise diffs and redo until understand. To motivate and hold I spent many working hours writing this tutorial, taking my treasure's better thecnics to you, that are in the future, learn to use them, so, give me some credits and efort when follow this tutorial, or, if prefers something easier, to follow [ckick here](http://bfy.tw/Mw0J) ...

If evething is ready, select you caracter and let's go ahead.

![Image](https://meta.gpupo.com/dockerized-helloworld/img/start.png)


## Containers and Sevices

:whale: **Dockerized Applications**  runs in [containers](https://www.docker.com/resources/what-container) and have a services set (**services**). Following the better practices, to any responsabilitie is created (preferably) a service.

Exemplifying, in a solution ([stack](https://en.wikipedia.org/wiki/Solution_stack)) popular like the tradicional [LAMP](https://en.wikipedia.org/wiki/LAMP_%28software_bundle%29), we got the following responsabilities:

1.  (**L**) Linux, operating systems that suport Filesystem, CLI tools, e suport to installed softwares;
2.  (**A**) Apache, a webserver installed and configured over OS (**L**);
3.  (**M**) *Data base* installed and configured over OS OS (**L**);
4.  (**P**) PHP, interpretator installed over **L**.

When converting this type of solution, we naturaly have to think in 4 services (L, A, M and P). However, L stop being fundamental because the suport to Filesystem and to Softwares already exists in the inherent dynamic of a image/container. So, the CLI tools stay under **Interpreter** (P) service's responsabilite.

Until here, our service set is like this:

1.  **Webserver** - Acessible on port 80, recieves requisitions, answers with processment made by Interpretator service.;
2.  **Interpretator** - Acessible only from **Webserver** or via docker exec, attends to calls from **Webserver**, connects to **Data Base**, have CLI tools;
3.  **Data Base**, Acessible only from **Interpretator** service.

This exemplify a  common solution, but let's go deeper on our work way:

If each service set have a **Webserver** that answer on programmer machine's port 80, so only one project can be up at time, or else each project needs a exclusive port. Imagine the caothic situation of this in a prodoction ambient. To solve this, each project recieves as a parameter a subdomain (ex: http://helloworld.localhost) and your webserver **do not** answer in a public port, but connects to service [httpd-gateway](https://opensource.gpupo.com/httpd-gateway/) that will do due routing as soon as it is required from configured broswer subdomain.

Also in this point, we have a question to be treated: **Data Base** service. In a Development ambient we need a funcional tests local base, development, unitary tests, etc... but in production ambient we do not need data base service because It runs in a different application local.

So we have two sets of services: one to development and other reduced for production ambient.

## Docker Compose File

This set are defined on ``docker-compose.yaml`` file, so in a project we have two versions of this configurations ``Resources/docker-compose.dev.yaml`` and ``Resources/docker-compose.prod.yaml`` and the developer create a simbolic link to project's root: :computer:

	ln -sn Resources/docker-compose.dev.yaml ./docker-compose.yaml

### NGINX + PHP-FPM + MariaDB

Still in our example, based on LAP stack conversion, we opted for using the [NGINX](https://www.nginx.com/) webserver instead Apache and, as we used PHP as a service, our option was [PHP-FPM](https://secure.php.net/manual/pt_BR/install.fpm.php). The data base is [MariaDB](https://mariadb.org/).

### Developement ambient X Production ambient

To complicate a little more, we know that in production ambient is not necessary that all packages from development ambient uses, so, our PHP service on developement set have mora things that the same service in production set.

To solve this, a image used by **Interpretator** service on development set is a image extension from ``PHP-FPM`` whith additions to developer.

For example, a extension ``php-xdebug`` exists in developement ambient's image but not in the used image for the production ambient.

Our services set ``DEV`` in this moment is like this:


1.  NGINX (**Webserver**) - Acessible via proxy, recieves requisitions, answer with processment done by Interpretator service;
2.  PHP-FPM (**Interpretator** )- Acessible only from **Webserver** or via docker exec, attends resuqests from  **Webserver**, connects to **Data base**, have CLI tools;
3.  MariaDB (**Data base**), Acessible only from **Interpretatos** service.

In example of configuration [Resources/docker-compose.dev.yaml](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Resources/docker-compose.dev.yaml) is used a gpupo/container-orchestration:symfony-dev image for ``php-fpm`` name service.

A public image [gpupo/container-orchestration:symfony-dev](https://hub.docker.com/r/gpupo/container-orchestration/tags) is a extension of oficial ``php-fpm`` image about debian adding necessary tools for PHP developement and also NodeJS activities to work with Webpack.

To padronize and make automatization easier, the interpretator service always receives the name "**php-fpm**".

This current project allows a [hands-on](https://en.wikipedia.org/wiki/Hands_on) according with this explanetion.

![Image](https://meta.gpupo.com/dockerized-helloworld/img/dockerized-stack.jpg)

---

## Running the application

**Step 1**, rise the [httpd-gateway](https://opensource.gpupo.com/httpd-gateway/): :computer:

	git clone https://github.com/gpupo/httpd-gateway.git;
	pushd httpd-gateway;
	make setup;
	make alone;
	popd;

**Step 2**, clone and rise this project: :computer:

	git clone https://github.com/gpupo-meta/dockerized-helloworld.git;
	cd dockerized-helloworld;
	docker-compose up -d;

**Step 3**, test the acess to http://dockerized-helloworld.localhost/helloworld.php or, if prefers, via comand line: :computer:

	curl http://dockerized-helloworld.localhost/helloworld.php

If everithing is working fine till here, on
http://dockerized-helloworld.localhost/phpinfo.php  you can acess informations about PHP service used.


**Step 4**, acess to service **Interpretator**'s terminal: :computer:

	docker-compose exec php-fpm bash

You will see that when executing this comand above is released to virtualized ambient where current directory is ``/var/www/app``.

If you list the directory's files ``/var/www/app`` you will see that they are **exactly** the same from this project root.

It happens for the fact that [we maped the directory](https://docs.docker.com/compose/compose-file/#volumes) on ``volumes`` existents parametrs on files __docker-compose*.yaml__

Even though you had installed in your operanting system a set of interpretators like PHP, preferably maintenance and execution commands related to project must be executed from the service (container), that have a version, configure and choosen tools to the project. After acess the service **Interpretator**'s terminal, install the dependêncies :whale: :

	make install

Now you can call project's APP CLI: :whale:

	bin/dockerized-helloworld

Execution of "Hello World" : :whale:

	bin/dockerized-helloworld greeting "Arnold Schwarzenegger"

## Recommended reading

* [Docker Quick Start](https://docs.docker.com/get-started/)

## Questions and answers

**Doubts?** If you need help to understand one of our concepts above, [create an issue](https://github.com/gpupo-meta/dockerized-helloworld/issues/new) an mark It with ``question`` **label**.

![Image](https://meta.gpupo.com/dockerized-helloworld/img/gameover.png)


# Javascript & CSS/ Webpack, SASS, ES2015

Starting from this point, exploration of a tarditional stack like LAMP already got behind.
Next we have increments that deals with the work form using the image [gpupo/container-orchestration:symfony-dev](https://hub.docker.com/r/gpupo/container-orchestration/tags) and others tools [opensource.gpupo.com](https://opensource.gpupo.com).

### Yarn/NPM/NodeJS

To management of CSS/Javascript dependencies we use YARN that is properly installed and configured on image gpupo/container-orchestration:symfony-dev used in developement stack PHP-FMP service.

So, as the comand ``composer install`` installs all **PHP** packages defineds in ``compose.json``, the comand ``yarn install`` installs **NPM** packages defined in ``package.json``: :whale:

	yarn install

Existing the necessity of add a package to project, we consulted https://www.npmjs.com/ or https://yarnpkg.com to find the package indentifier. Example: ``babel-plugin-transform-es2015-parameters`` :whale:.

	yarn add babel-plugin-transform-es2015-parameters --dev

The example above add a package that is loaded only on developement ambient as we used the parameter ``--dev``.

#### Building

Starting from instructions of ``assets/js/helloworld.js`` the file ``public/build/helloworld.min.js`` will be compiled : :whale:

	yarn build

We can test the results in the following way : :whale:

	nodejs public/build/helloworld.min.js

### Babel/ES2015

A modern javascript code write uses ``ES6`` known as ``ECMAScript 6`` or ``ES2015``. Here enters [Babel](https://babeljs.io/), a Javascript compiler that allow us use a series of resources ``ES6``.

I will not detail the ``ES6`` use in this document but right below are link for the sintax learning.

In all project ``dockerized-helloworld`` all necessary tolls to compile Javascript ``ES6`` were installed when you executed ``yarn install``.

The Javascript ``assets/js/helloworld-ES2015.js`` were compiled for ``yarn build`` in ``public/build/helloworld-ES2015.min.js``.

We can test the result in the following way : :whale:

	nodejs public/build/helloworld-ES2015.min.js

Of course, for evething works, necessary configuration were made in ``.babelrc`` file (compilation instructions), ``package.json`` (NPM's installed packages) and ``webpack.config.js`` (wich files compile and where do outputs) and I will not talk about configuration but I will leave some links in *recommended readings* that treats of this.

### SASS

The [Sass](https://sass-lang.com/) is a based CSS language that, after compiled, generate the traditional CSS.

The ``assets/scss/app.scss`` file includes all of Bootstrap 4 CSS (Avaiable in packages configuration and inatlled for ``yarn install``) and some compiled example code on path ``public/build/app.min.css``.

### Webpack

The ``yarn build`` magic happens because [webpack](https://webpack.js.org/) compile and minimize our javascript and SASS files. More than this, It receives the indications that ``assets/scss/app.scss`` is being required for ``assets/js/app.js`` and include It on build process.

![Webpack flow image](https://webpack.github.io/assets/what-is-webpack.png)

Your configuration is done starting from ``webpack.config.js`` file.

You can trigger the webpack directly in the following way : :whale:

	export PATH="$(yarn bin):$PATH";
	webpack --config webpack.config.js;

This is very useful to test new configurations.

To see a page that load javascript and css compiled, open http://dockerized-helloworld.localhost/bootstrap.php .

### More recomende reading

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

Starting from now, we will include news ``services`` in our project and, to this, we will stop using the actual ``docker-compose file`` and will use ``Resources/docker-compose.extra-services.yaml`` file.

To this, let's follow the configuration steps:

**Step 1**, Drop actual services : :computer:

	docker-compose down

**Step 2**, replace the [simbolic link](https://www.shellhacks.com/symlink-create-symbolic-link-linux/)  of ``docker-compose.yaml`` (that currently points to ``Resources/docker-compose.dev.yaml``) to ``Resources/docker-compose.extra-services.yaml`` : :computer:

	ln -snf Resources/docker-compose.extra-services.yaml ./docker-compose.yaml

**Step 3**, Rise the services : :computer:

	docker-compose up -d

### PhpMyAdmin (extra)

Now, in subdomain, [phpmyadmin-dockerized-helloworld.localhost](http://phpmyadmin-dockerized-helloworld.localhost) you can acess the [PhpMyAdmin](https://www.phpmyadmin.net/)

In the [Resources/docker-compose.dev.yaml](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Resources/docker-compose.dev.yaml) file I include the service that offers **PhpMyAdmin**, using Docker oficial image.

### Redis

The [Redis](https://aws.amazon.com/pt/elasticache/what-is-redis/) is storage of data structure of open code and memory key-value and we used frequently in PHP applications to replace the [APC Cache](https://www.php.net/manual/en/book.apc.php).

## Logstash

### Logs percistence in RELK stack

Traditionaly, a application record logs in a file, for example, a [Symfony 4](https://symfony.com/) application will record Its logs in ``var/logs/dev.log``, ``var/logs/prod.log`` ou ``var/logs/dev.log``, but we are projecting an application that run in containers, we need a better way to store this logs because, one of the fundamentals of containers using is that each container is projected to attend a processment in a determinated time and **is disposable**. We can not lose logs of application each times that we recreate a container or change the base image of Its. We could solve this problem with map thecnics and cloud arquitecture, using many machines, so we will have to open many directories to search information and so on certain amount of machines, do this is impracticable. So, a engenniring that solve in a very skilful way this problem is to send all the logs to a [logs server](https://en.wikipedia.org/wiki/Server_log). That simple. : :boom:

To mount this server we use, basically, 4 ``services``:

![RELK flow image](https://meta.gpupo.com/dockerized-helloworld/img/relk.jpg)

1.  (**R**) RabbitMQ;
2.  (**E**) Elasticsearch;
3.  (**L**) Logstash;
4.  (**K**) Kibana.

Our application, using a specific drive ([php-amqplib/php-amqplib](https://github.com/php-amqplib/php-amqplib)), send logs to the [RabbitMQ](https://www.rabbitmq.com/) server, that mantain it on a row.

The [Logstash](https://www.elastic.co/products/logstash) connects on **RabbitMQ** and collect the log resgister, (transforming then, if it is necessary) and register them on [Elasticsearch](https://www.elastic.co/). The [Kibana](https://www.elastic.co/products/kibana) is a reading inteface
 and logs exploration recorded on **Elasticsearch**.

#### Rising the stack

To our :whale: **Dockerized Application** is not interesting responsabilizar for configuration of **stack RELK**, so I prepared this services set in a secret place that you will simple rise with the followin setup: :computer:

	make relk@up

Logstash config: :whale:

```bash
curl -XPOST -D- 'http://kibana:5601/api/saved_objects/index-pattern' \
	-H 'Content-Type: application/json' \
	-H 'kbn-version: 6.2.4' \
	-d '{"attributes":{"title":"logstash-*","timeFieldName":"@timestamp"}}'
```

Now let's test the logs send: :whale:

	bin/dockerized-helloworld log:generator 100

##### Sevices dashboards acess of RELK stack

If everythong woks fine, you will have acess to services:

* [RabbitMQ](http://dockerized-helloworld.localhost:15672/), user ``admin``, password ``d0ck3r1zzd``
* [Kibana](http://dockerized-helloworld.localhost:5601)
* [Logstash API](http://dockerized-helloworld.localhost:9600/_node/hot_threads?human=true)

To our tutorial, the most important is that you get vizualize the generate logs by application and, for this, you should acess the Kibana and choose the menu item **Discover**. You must see one screem like this one:

![Kibana dashboard image](https://meta.gpupo.com/dockerized-helloworld/img/kibana.png)

:memo: This is the same logic of send the logs to a centralized local can be adopted for any software, not only for PHP Apps. The [httpd-gateway](https://opensource.gpupo.com/httpd-gateway/) is ready to send logs from to a logs server, in a production ambient.

#### Some tips about logs

1. Many information is noise and few information is inappropriate. It is hard to find the idel balance but, this is the chalenge. In the case of micro services, also think on traceability  bettwen services, like a use of a ``service`` identifier. A other thing to keep in mind is that logs are temporals, not permanet, with a lifespan of some months.
2. Follow [severity levels](https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md#log-levels) (Syslog Protocol).
3. Estructure your logs. Follow a concerted JSON pattern to record. It make easier the analisys and search.
4. Record the logs with care if it do not injure the performance.
5. Considerer that the log server can stay unavailable and your application must resist this.
6. In a solid PHP application, use the [Monolog](https://github.com/Seldaek/monolog/). See [Symfony Guide Logging](https://symfony.com/doc/current/logging.html).

#### Recommended reading

* [Tutorials for using RabbitMQ in various ways](http://www.rabbitmq.com/getstarted.html)
* [Tutorial RabbitMQ X PHP](https://www.rabbitmq.com/tutorials/tutorial-one-php.html)

#### Others persistences

In fact you already used to persist out of a application informations like relacional data base. An important arctifact to persist externaly are static files send for user starting from uploaded form, for example.
To attends the demand I use the [Content Butler](https://github.com/gpupo/content-butler) associado ao [Doctrine PHP Content Repository ODM](https://www.doctrine-project.org/projects/doctrine-phpcr-odm/en/latest/index.html) project that treats assets like objects and management then in a [Apache Jackrabbit](https://jackrabbit.apache.org/jcr/index.html) server.

---

### Make

[Make](https://en.wikipedia.org/wiki/Make_%28software%29) is a tool to automation of build, create in 1976 and designed to develop to sove problems during the building process, originally used in [C language](https://en.wikipedia.org/wiki/C_%28programming_language%29) projects and that started do be widely used in[Unix Like](https://en.wikipedia.org/wiki/Unix-like) projects.

Your configuration file is the [Makefile](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Makefile) that is on this project's root. In this file we configureted ``targets``. Each target is a sequence of instructions that can in turn rely on other targets.

The target sintaxe is:

```make
## Coment
target: [prerequisite]
    command1
    [command2]
```

Due to the customized configuration of our [Makefile](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Makefile), if you simply execute ``make`` without especify each target you want to trigger, a list of targets and their description will be displayed. Try it : :whale:

	make

![Make output](https://meta.gpupo.com/dockerized-helloworld/img/make.png)


In fact, at the begining of this tutorial I ask to execute ``make install``. It fired the target on **install** configurating it on Makefile:

```make
## Composer Install
install:
	composer self-update
	composer install --prefer-dist
```
The target **install** follow the script of atualize the [Composer](https://getcomposer.org/) and install the PHP dependencies. If the target goal was install all taht the project needs, what make sense in a real project, we could add the call for installation the NPM packages and the necessity of perform a build after installation:

```make
## Install project's needed dependencies
install:
	composer self-update
	composer install --prefer-dist
	yarn install
	yarn build
```

Try the target ``bash`` that will realese you directly on service bash  ``PHP-FPM``:

	make bash

## QA Tools

The image [gpupo/container-orchestration:symfony-dev](https://hub.docker.com/r/gpupo/container-orchestration/tags) have [quality assurance](https://en.wikipedia.org/wiki/Software_quality_assurance) tools that help us to maintain the quality writing and Engineering.

### Coding Standard

In this project we follow [PHP Standards Recommendations](https://www.php-fig.org/psr/)(PSR) and also
suggested patterns for the [Symfony](https://symfony.com/) project with the goal of make easier the reuse of code betwen many project that implements detemrninaded pattern.

If you are not acquainted with PSRs yet, know that exists PSRs for implementaions of [autoload](http://br1.php.net/manual/en/function.autoload.php)[ (](http://www.php-fig.org/psr/psr-4/)[PSR-4](http://www.php-fig.org/psr/psr-4/)), code styles suggests, like key position, identation ([use tabs or spaces?](http://www.jwz.org/doc/tabs-vs-spaces.html)) ([PSR-1](http://www.php-fig.org/psr/psr-1/) e [PSR-2](http://www.php-fig.org/psr/psr-2/)).

Exist also proposals in draft to standardization of documentations docblock ([PSR-5](https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md)) and a interface for HTTP requisitions ([PSR-7](https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md))

More informations read [FAQ](https://www.php-fig.org/faqs/) and make us a visit [GitHub repo](https://github.com/php-fig/fig-standards) with patterns already accepted.

#### Main writing standards adopted in this project

*   [PSR-1: Basic Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
*   [PSR-2: Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
*   [PSR-4: Autoloading Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
*   [PSR-5: PHPDoc (draft)](https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md)
*   [Symfony Coding Standards](https://symfony.com/doc/current/contributing/code/standards.html)

#### php-cs-fixer

A very important tool is the [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
that will align the write code according to the default rules selected for the project.
In the [.php_cs.dist](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/.php_cs.dist) file is configureted this rules set.

Let's go to a pratical example! Although works, the [src/Traits/VeryWrongCodeStyleTrait.php](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/src/Traits/VeryWrongCodeStyleTrait.php) file is bad writen and gnores many write patterns. But, What standards are these?
Run the [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer): :whale:

	make php-cs-fixer

If you execute ``git diff`` you see something like this:

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

In this diff the file received modifications:

* Added a declaration ``declare(strict_types=1);``.
* Added a pattern *HEADER* to all the project PHP files.
* Organized in alphabetic order the declarations use.
* Write with a ``use`` per line, like ask the configureted CS.
* Removed the ``use PDO`` because the PDO class not reveive none use on file line.
* Changed the ``{`` of place, according with codding style defined.
* Added the final dot to documentation lines.

:memo: It is a ggo practice you use the ``make php-cs-fixer`` after finifh the developemnet of a PHP feature

To roll back the class to the previous state and allow you enjoy this tutorial:

	git checkout src/Traits/VeryWrongCodeStyleTrait.php

#### PHP_CodeSniffer

A other important tool is the [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) that also will help us to maintain a according pattern and is configurated on [phpcs.xml.dist](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/phpcs.xml.dist) file.

Try: :whale:

	make phpcbf

The php-cs-fixer, PHP_CodeSniffer tools complement themselves and one can point an inprovement that the other did not get. With ``make`` we can create a ``target`` that is a collection of other ``targets``. In our Makefile, the target **cs** do this: :whale:

	make cs

### PHPMD

The [PHPMD](https://phpmd.org/) - Ruleset for PHP Mess Detector that enforces coding standards is configurated on [.phpmd.xml](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/.phpmd.xml) file. :whale:

	make phpmd

### Phan

[Phan](https://github.com/phan/phan), static analyzer para o PHP. Is configurated on [config/phan.php ](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/config/phan.php) file and points improvements and possibles application errors: :whale:

	make phan

### PHPSTAN

	make phpstan

### PHPLOC

	make phploc

---

### Unitary tests

The Makefile is configured to run the unitrary tests: :whale:

	make phpunit

You will see something like this output:

![Image](https://meta.gpupo.com/dockerized-helloworld/img/phpunit.png)


The ``tests/`` directory keeps the unitary tests executed by [phpunit](https://phpunit.de/). Although exist just the [tests/Console/Command/GreetingCommandTest.php](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/tests/Console/Command/GreetingCommandTest.php) test that validate the emited salutation for the console comand ``bin/dockerized-helloworld greeting `` previouly executed, some interesting thecnics are used, like repeated validation using [dataproviders](https://phpunit.readthedocs.io/en/8.0/writing-tests-for-phpunit.html#data-providers) and the outout test of a [Command](https://symfony.com/doc/current/console.html).

 Let's go to something more simple ...

#### Automatic skeletons

Our [dockerized-helloworld project](https://github.com/gpupo-meta/dockerized-helloworld) uses a [gpupo/common](https://opensource.gpupo.com/common/) that have the comand ``vendor/bin/developer-toolbox`` that can help us to create a unitary test skeleton starting from a existent class.

If we want to create a unitary test to the object ``Gpupo\DockerizedHelloworld\Entity\Person``: :whale:

	vendor/bin/developer-toolbox generate --class='Gpupo\DockerizedHelloworld\Entity\Person'

The comand above generate the ``tests/Entity/PersonTest.php`` flie that must contain a content like this:


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

Execute the unitary tests: :whale:

	make phpunit

Remember that the ``tests/Entity/PersonTest.php`` file is a initial draft and you need keep Its development to transform it in a quality test.

When you try edit the ``tests/Entity/PersonTest.php`` file in your IDE, you will not be able to record the changes, what take us to the next phase ...

![Image](https://meta.gpupo.com/dockerized-helloworld/img/permission.png)


#### The permission problem

The genreted files from the [shell session](https://superuser.com/questions/651111/what-is-the-definition-of-a-session-in-linux) of :whale: container do not have the same owner that the files generated on host machine :computer: session. It happens bacause linux users are different in each session. In a very common case, the file generated from the contairner's session will belong to constainer's root and to the host root, and the actual user, on the host machine that could not edit it. Exist many forms of work around this.I will be agressive, on the work around choose, saying to the project "give me all the things here, because it is mine!" with sudo + chown: :computer:

	sudo chown -R $USER:$USER ./

....

---

# CFinal considerations

![Image](https://meta.gpupo.com/dockerized-helloworld/img/congrats.jpg)


Very well, you have reset the game. :) :checkered_flag:

You can contribute with this project by creating a [Pull Request](https://help.github.com/en/articles/creating-a-pull-request) or informing a bug/enhancement in [issues](https://github.com/gpupo-meta/dockerized-helloworld/issues). This includes typo.

See a [enhancement list](https://github.com/gpupo-meta/dockerized-helloworld/labels/enhancement) that needs developement.

### Shutdown

To shutdown all containers rised during the execution of this tutorial, you can use the comand: :computer:

	docker stop $(docker ps -a -q)

:memo: Now an extra, if you configurated your computer with [ gpupo-meta/setup-machine](https://github.com/gpupo-meta/setup-machine), just execute:  :computer:

	docker-stop-all

After load millions of Docker images bits, maybe you need free removing all Docker images in cache: :computer:

	docker-remove-all


---
#### Table of contents

<div id="inline_toc" markdown="1">
* TOC
{:toc}
</div>

---

![Cya image](https://meta.gpupo.com/dockerized-helloworld/img/pizzatime.jpg)

