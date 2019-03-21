# dockerized-helloworld

## Conceito

Dockerized Applications rodam em [containers](https://www.docker.com/resources/what-container) e possuem um conjunto de serviços. Seguindo as melhores práticas, para cada responsabilidade é criado (preferencialmente) um serviço.

Exemplificando, em uma solução popular como a tradicional [LAMP](https://en.wikipedia.org/wiki/LAMP_%28software_bundle%29), temos as seguintes responsabilidades:

1.  (L) Linux, sistema operacional com suporte ao Filesystem, Ferramentas CLI, e  suporte aos softwares instalados;
2.  (A) Apache, um webserver instalado e configurado sobre o OS (L);
3.  (M) *Banco de dados* instalado e configurado sobre o OS (L);
4.  (P) PHP, interpretador instalado sobre L.

Ao convertermos esse tipo de solução, devemos naturalmente pensar em 4 serviços (L, A, M e P). Entretanto, L deixa de ser fundamental pois o suporte ao Filesystem e aos Softwares já existe na dinâmica inerente de uma imagem/container. Então, as Ferramentas CLI ficam sob responsabilidade de serviço *Interpretador* (P).

Até aqui, nosso conjunto de serviços está assim:

1.  *Webserver* - Acessível na porta 80, recebe as requisições, responde com processamento feito pelo serviço interpretador;
2.  *Interpretador* - Acessível somente pelo *Webserver* ou via docker exec, atende a pedidos do *Webserver*, conecta-se ao *Banco de dados*, possui ferramentas CLI;
3.  *Banco de dados*, acessível somente pelo serviço *Interpretador* .

Isso exemplifica uma solução comum, mas vamos nos aprofundar um pouco no nosso modo de trabalho:

Se cada conjunto de serviços possui um *Webserver* que responde na porta 80 da máquina do programador, então somente um projeto pode estar levantado por vez, ou então cada projeto precisa de uma porta exclusiva. Imagine a situação caótica disto em um ambiente de produção. No nosso modo de trabalho, cada projeto recebe como parâmetro um subdominio (ex: http://helloworld.localhost) e seu webserver não atende em porta pública, mas sim conecta-se ao serviço [httpd-gateway](https://opensource.gpupo.com/httpd-gateway/) (ver Ambiente de trabalho >> *Webserver* local) que fará o devido roteamento assim que o browser requisitar pelo subdominio configurado.

Também neste ponto, temos uma questão a ser tratada: O serviço do banco de dados. Em um ambiente de desenvolvimento, precisamos de uma base local para testes funcionais, desenvolvimento, testes unitários, etc ... mas no ambiente de produção não precisamos do serviço de banco de dados pois este roda em local diferente da aplicação.

Então possuímos dois conjuntos de serviços: Um para desenvolvimento e outro reduzido para o ambiente de produção.

### Docker Compose File

Esses conjuntos são definidos no arquivo docker-compose.yaml, então em um projeto temos duas versões destas configurações ``Resources/docker-compose.dev.yaml`` e ``Resources/docker-compose.prod.yaml`` e o desenvolvedor faz um link simbólico para a raiz do projeto:

	ln -sn Resources/docker-compose.dev.yaml ./docker-compose.yaml

Ainda no nosso exemplo, baseado na conversão de uma stack LAMP, optamos por utilizar o webserver [NGINX](https://www.nginx.com/)ao invés do Apache e como usamos o PHP como serviço, nossa opção é pelo [PHP-FPM](https://secure.php.net/manual/pt_BR/install.fpm.php). A base de dados é [MariaDB](https://mariadb.org/).

Para complicar um pouco mais, sabemos que no ambiente de produção na é necessário todos os pacotes que o ambiente de desenvolvimento utiliza, então, nosso serviço PHP no conjunto do desenvolvimento possui mais coisas que o mesmo serviço do conjunto de produção.

Para resolver isso, a imagem utilizada pelo serviço *Interpretador*  no conjunto de desenvolvimento é um extensão da PHP-FPM com aditivos para o desenvolvedor.

Por exemplo, a extensão ``php-xdebug`` existe na imagem do ambiente de desenvolvimento mas não na imagem usada em produção.

Nosso conjunto de serviços DEV neste momento está assim:

1.  NGINX (*Webserver*) - Acessível via proxy, recebe as requisições, responde com processamento feito pelo serviço interpretador;
2.  PHP-DEV (*Interpretador* )- Acessível somente pelo *Webserver* ou via docker exec, atende a pedidos do *Webserver*, conecta-se ao *Banco de dados*, possui ferramentas CLI;
3.  MariaDB (*Banco de dados*), acessível somente pelo serviço *Interpretador* .

Em alguns projetos que usam apenas linha de comando (CLI), o serviço *Webserver* é dispensável. No exemplo de configuração [Resources/docker-compose.dev.yaml](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Resources/docker-compose.dev.yaml)usa-se a imagem gpupo/container-orchestration:symfony-dev para o serviço de nome ``php-fpm``.

A imagem pública [gpupo/container-orchestration:symfony-dev](https://hub.docker.com/r/gpupo/container-orchestration/tags) é uma extensão da imagem oficial ``php-fpm`` sobre debian com a adição de ferramentas necessárias ao desenvolvimento PHP e também de atividades com NodeJS para trabalho com o Webpack.

Para padronizar e facilitar automatização, o serviço do interpretador sempre recebe o nome "*php-fpm*".

Este atual projeto possibilita um "mão na massa" de acordo com essa explicação.
