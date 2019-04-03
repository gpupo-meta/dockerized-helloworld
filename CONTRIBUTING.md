# Como contribuir

## Fork

Se você quer contribuir com este projeto, [faça um fork](https://help.github.com/en/articles/fork-a-repo) para sua conta pessoal.

Depois, clone seu fork em seu computador.

Abra uma brach local. Exemplo:

	git checkout -b feature-melhoria-layout

Faça suas modificações e commits.
Envie sua branch local para sua fork:

	git push origin feature-melhoria-layout:feature-melhoria-layout

Depois crie uma [Pull Request](https://help.github.com/en/articles/creating-a-pull-request)	de sua nova branch
para a ``master`` deste projeto.

## Build

Se você editou algum conteúdo em arquivos de ``templates/`` será necessário rodar o build:

	make build

---

## Website

O Website [meta.gpupo.com/dockerized-helloworld/](https://meta.gpupo.com/dockerized-helloworld/) é gerado pelo [Jekyll](https://jekyllrb.com/) que roda nos servidores do Github.

O arquivo ``docs/_layouts/default.html`` define o layout principal.

Para facilitar o desenvolvimento da template do website, usamos um ``container`` com a imagem [Starefossen/docker-github-pages](https://github.com/Starefossen/docker-github-pages) e algumas configurações que nos permitem acessar o website na porta ``4000``.

### Setup

Criar o arquivo ``docs/.env.local`` e acrescentar a variável ``JEKYLL_GITHUB_TOKEN`` com seu [personal token](https://github.com/settings/tokens/new).

Exemplo:

	cat docs/.env.local

	JEKYLL_GITHUB_TOKEN=your_personal_api_token

### Rodar

Para levantar o server Jekyll:

	make server@run

Se o server subiu sem problemas, você poderá acessar em [http://0.0.0.0:4000/pt_BR.html](http://0.0.0.0:4000/pt_BR.html)		
o conteúdo em português.

Para parar o servidor Jekyll:

	make server@down
