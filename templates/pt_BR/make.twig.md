[Make](https://en.wikipedia.org/wiki/Make_%28software%29) é uma ferramenta para automatização de build criada em 1976 e desenhada para resolver problemas durante o processo de build, originalmente usada em projetos de [linguagem C](https://en.wikipedia.org/wiki/C_%28programming_language%29) e que passou a ser amplamente utilizada em projetos [Unix Like](https://en.wikipedia.org/wiki/Unix-like).

Seu arquivo de configuração é o [Makefile](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Makefile) que está na raiz deste projeto. É nesse arquivo que configuramos ``targets``. Cada target é uma sequencia de instruções, que pode por sua vez depender de outros targets.

A sintaxe de um target é:

```make
## Coment
target: [prerequisite]
    command1
    [command2]
```

Devido à configuração customizada de nosso [Makefile](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/Makefile), se você simplesmente executar ``make`` sem especificar qual target quer acionar, uma lista de targets e suas descrições será exibida. Experimente : :whale:

	make

![Make output](https://meta.gpupo.com/dockerized-helloworld/img/make.png)

Na verdade, logo no começo deste tutorial eu pedi para que você executace ``make install``. Isto fez com que fosse acionado o target **install** configurado no Makefile:

```make
## Composer Install
install:
	composer self-update
	composer install --prefer-dist
```
O target **install** segue o script de atualizar o [Composer](https://getcomposer.org/) e instalar as dependências PHP. Se o objetivo deste target fosse de instalar tudo o que o projeto precisa, o que faz sentido em um target destes em um projeto real, poderíamos acrescentar a chamada para instalação dos pacotes NPM e ainda a necessidade de realizar o build após instalação:

```make
## Instala as dependências o que o projeto precisa
install:
	composer self-update
	composer install --prefer-dist
	yarn install
	yarn build
```

Experimente o target ``bash`` que vai lhe lançar diretamente no bash do serviço ``PHP-FPM``:

	make bash
