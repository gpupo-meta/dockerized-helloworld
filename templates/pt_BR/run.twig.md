**Passo 1**, levantar o [httpd-gateway](https://opensource.gpupo.com/httpd-gateway/): :computer:

	git clone https://github.com/gpupo/httpd-gateway.git;
	pushd httpd-gateway;
	make setup;
	make alone;
	popd;

**Passo 2**, clonar e levantar este projeto: :computer:

	git clone https://github.com/gpupo-meta/dockerized-helloworld.git;
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
