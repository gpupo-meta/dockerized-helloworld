# Como contribuir

## Build

Se você editou algum conteúdo em arquivos de ``templates/`` será necessário rodar o build:

	make build

## Website

O Website [meta.gpupo.com/dockerized-helloworld/](https://meta.gpupo.com/dockerized-helloworld/) é gerado pelo [Jekyll](https://jekyllrb.com/) que roda nos servidores do Github.

Para facilitar o desenvolvimento da template do website, usamos um ``container`` com a imagem [Starefossen/docker-github-pages](https://github.com/Starefossen/docker-github-pages) e algumas configurações que nos permitem acessar o website na porta ``4000``.

Para levantar o server Jekyll:

	make server@run

Se o server subiu sem problemas, você poderá acessar em [http://0.0.0.0:4000/pt_BR.html](http://0.0.0.0:4000/pt_BR.html)		
o conteúdo em português.
