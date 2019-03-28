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
