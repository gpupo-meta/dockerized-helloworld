### Persistência de logs na stack RELK

Tradicionalmente, uma aplicação grava logs em um arquivo como por exemplo, uma aplicação [Symfony 4](https://symfony.com/) gravará seus logs em ``var/logs/dev.log``, ``var/logs/prod.log`` ou ``var/logs/dev.log``, mas nós que estamos projetando uma aplicação que roda em containers precisamos de uma forma melhor de armazenar estes registros pois, um dos fundamentos do uso de containers é que cada container é projetado para atender um processamento por um tempo determinado e **é descartável**. Não podemos perder os logs da aplicação a cada vez que recriamos um container ou mudamos a imagem na qual ele é baseado. Poderíamos resolver este problema com a técnica de mapeamento de um diretório da máquina host para ``var/logs`` mas isto continua centralizando os logs em uma máquina e em uma arquitetura de nuvem, usamos muitas máquinas, então teríamos que abrir muitos diretórios para buscar uma informação e a partir de certa quantidade de máquinas, fazer isto se torna inviável. Então, uma engenharia que resolve de maneira muito habilidosa este problema é enviar todos os logs para um [servidor de logs](https://en.wikipedia.org/wiki/Server_log). Simples assim. :boom:

Para montar esse servidor usamos basicamente 4 ``services``:

{% include 'templates/includes/relk-flow.twig.md' %}

Nossa aplicação, utilizando um drive específico ([php-amqplib/php-amqplib](https://github.com/php-amqplib/php-amqplib)), envia os logs para um servidor [RabbitMQ](https://www.rabbitmq.com/) que os guarda em uma fila.

O [Logstash](https://www.elastic.co/products/logstash) conecta no **RabbitMQ** e coleta os registros de log, (transforma-os se necessário) e grava-os no [Elasticsearch](https://www.elastic.co/). O [Kibana](https://www.elastic.co/products/kibana) é uma interface de leitura e exploração destes logs que estão gravados no **Elasticsearch**.

#### Levantando a stack

Para nossa :whale: **Dockerized Application** não é interessante responsabilizar-se pela configuração da **stack RELK**, então eu preparei este conjunto de serviços em um lugar secreto, que você simplesmente vai levantar com o seguinte setup: :computer:

{% include 'templates/includes/relk-up.twig.md' %}

Agora vamos testar o envio de logs: :whale:

	bin/dockerized-helloworld log:generator 100

##### Acesso aos dashboards dos serviços da stack RELK

Se tudo correu bem você terá acesso aos serviços:

{% include 'templates/includes/relk-links.twig.md' %}

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
