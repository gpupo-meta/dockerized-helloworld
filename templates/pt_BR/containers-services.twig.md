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
