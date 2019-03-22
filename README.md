## Containers e Serviços

Dockerized Applications rodam em [containers](https://www.docker.com/resources/what-container) e possuem um conjunto de serviços. Seguindo as melhores práticas, para cada responsabilidade é criado (preferencialmente) um serviço.

Exemplificando, em uma solução ([stack](https://en.wikipedia.org/wiki/Solution_stack)) popular como a tradicional [LAMP](https://en.wikipedia.org/wiki/LAMP_%28software_bundle%29), temos as seguintes responsabilidades:

1.  (**L**) Linux, sistema operacional com suporte ao Filesystem, Ferramentas CLI, e  suporte aos softwares instalados;
2.  (**A**) Apache, um webserver instalado e configurado sobre o OS (**L**);
3.  (**M**) *Banco de dados* instalado e configurado sobre o OS (**L**);
4.  (**P**) PHP, interpretador instalado sobre **L**.

Ao convertermos esse tipo de solução, devemos naturalmente pensar em 4 serviços (L, A, M e P). Entretanto, L deixa de ser fundamental pois o suporte ao Filesystem e aos Softwares já existe na dinâmica inerente de uma imagem/container. Então, as Ferramentas CLI ficam sob responsabilidade do serviço *Interpretador* (P).

Até aqui, nosso conjunto de serviços está assim:

1.  *Webserver* - Acessível na porta 80, recebe as requisições, responde com processamento feito pelo serviço interpretador;
2.  *Interpretador* - Acessível somente pelo *Webserver* ou via docker exec, atende a pedidos do *Webserver*, conecta-se ao *Banco de dados*, possui ferramentas CLI;
3.  *Banco de dados*, acessível somente pelo serviço *Interpretador* .

Isso exemplifica uma solução comum, mas vamos nos aprofundar um pouco no nosso modo de trabalho:

Se cada conjunto de serviços possui um *Webserver* que responde na porta 80 da máquina do programador, então somente um projeto pode estar levantado por vez, ou então cada projeto precisa de uma porta exclusiva. Imagine a situação caótica disto em um ambiente de produção. Para resolver isso, cada projeto recebe como parâmetro um subdominio (ex: http://helloworld.localhost) e seu webserver *não* atende em porta pública, mas sim conecta-se ao serviço [httpd-gateway](https://opensource.gpupo.com/httpd-gateway/) que fará o devido roteamento assim que o browser requisitar pelo subdominio configurado.

Também neste ponto, temos uma questão a ser tratada: O serviço do *Banco de dados*. Em um ambiente de desenvolvimento, precisamos de uma base local para testes funcionais, desenvolvimento, testes unitários, etc ... mas no ambiente de produção não precisamos do serviço de banco de dados pois este roda em local diferente da aplicação.

Então possuímos dois conjuntos de serviços: Um para desenvolvimento e outro reduzido para o ambiente de produção.

## Docker Compose File

Esses conjuntos são definidos no arquivo ``docker-compose.yaml``, então em um projeto temos duas versões destas configurações ``Resources/docker-compose.dev.yaml`` e ``Resources/docker-compose.prod.yaml`` e o desenvolvedor faz um link simbólico para a raiz do projeto:

	ln -sn Resources/docker-compose.dev.yaml ./docker-compose.yaml

## NGINX + PHP-FPM + MariaDB

Ainda no nosso exemplo, baseado na conversão de uma stack LAMP, optamos por utilizar o webserver [NGINX](https://www.nginx.com/) ao invés do Apache e como usamos o PHP como serviço, nossa opção é pelo [PHP-FPM](https://secure.php.net/manual/pt_BR/install.fpm.php). A base de dados é [MariaDB](https://mariadb.org/).

## Ambiente de desenvolvimento X Ambiente de produção

Para complicar um pouco mais, sabemos que no ambiente de produção não é necessário todos os pacotes que o ambiente de desenvolvimento utiliza, então, nosso serviço PHP no conjunto do desenvolvimento possui mais coisas que o mesmo serviço do conjunto de produção.

Para resolver isso, a imagem utilizada pelo serviço *Interpretador*  no conjunto de desenvolvimento é uma extensão da imagem ``PHP-FPM`` com aditivos para o desenvolvedor.

Por exemplo, a extensão ``php-xdebug`` existe na imagem do ambiente de desenvolvimento mas não na imagem usada no ambiente de produção.

Nosso conjunto de serviços ``DEV`` neste momento está assim:

1.  NGINX (**Webserver**) - Acessível via proxy, recebe as requisições, responde com processamento feito pelo serviço interpretador;
2.  PHP-FPM (**Interpretador** )- Acessível somente pelo **Webserver** ou via docker exec, atende a pedidos do **Webserver**, conecta-se ao **Banco de dados**, possui ferramentas CLI;
3.  MariaDB (**Banco de dados**), acessível somente pelo serviço **Interpretador** .

No exemplo de configuração [Resources/docker-compose.dev.yaml](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Resources/docker-compose.dev.yaml) usa-se a imagem gpupo/container-orchestration:symfony-dev para o serviço de nome ``php-fpm``.

A imagem pública [gpupo/container-orchestration:symfony-dev](https://hub.docker.com/r/gpupo/container-orchestration/tags) é uma extensão da imagem oficial ``php-fpm`` sobre debian com a adição de ferramentas necessárias ao desenvolvimento PHP e também de atividades com NodeJS para trabalho com o Webpack.

Para padronizar e facilitar automatização, o serviço do interpretador sempre recebe o nome "*php-fpm*".

Este atual projeto possibilita um [mão na massa](https://en.wikipedia.org/wiki/Hands_on) de acordo com essa explicação.

---

## Setup

Este projeto considera que você já possui o Docker e o Docker Compose instalado em seu sistema operacional(veja [ gpupo-meta/setup-machine](https://github.com/gpupo-meta/setup-machine)).

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

**Passo 3**, testar o acesso a http://dockerized-helloworld.localhost ou se preferir, via linha de comando:

	curl http://dockerized-helloworld.localhost

## Execução

Se tudo correu bem até aqui, em
http://dockerized-helloworld.localhost/phpinfo.php você acessa informações sobre o serviço PHP em uso.

Em http://phpmyadmin-dockerized-helloworld.localhost você poderá acessar o [PhpMyAdmin](https://www.phpmyadmin.net/)

**Passo 4**, acesso ao terminal do serviço *Interpretador*:

	docker-compose exec php-fpm bash

Você verá que ao executar o comando acima é lançado para o ambiente virtualizado onde o diretório atual é ``/var/www/app``.

Se você listar os arquivos do diretório  ``/var/www/app`` verá que são **exatamente** os mesmos da raiz deste projeto.
Isto se dá pelo fato que que [mapeamos o diretório](https://docs.docker.com/compose/compose-file/#volumes) nos parâmetros ``volumes`` existentes nos arquivos __docker-compose*.yaml__


Apesar de você ter instalado em seu sistema operacional, todo um conjunto de interpretadores como por exemplo o PHP, preferenciamente os comandos de manutenção e execução relacionados ao projeto devem ser executados a partir do serviço (container), que possui a versão, configuração e ferramentas escolhidas para o projeto. Após acessar o terminal do serviço *Interpretador*, instale as dependências:

	make install


Você pode agora chamar o APP CLI deste projeto:

	bin/dockerized-helloworld

Execução do "Hello World":

	bin/dockerized-helloworld greeting "Arnold Schwarzenegger"

---

## Contribuição

Você pode contribuir com este projeto criando uma [Pull Request](https://help.github.com/en/articles/creating-a-pull-request)
