A partir deste momento vamos incluir novos ``services`` em nosso projeto e para isso vamos deixar de usar o ``docker-compose file`` atual e passaremos a usar o arquivo ``Resources/docker-compose.extra-services.yaml``.

Para isso, vamos aos passos de configuração:

**Passo 1**, derrube os serviços atuais : :computer:

	docker-compose down

**Passo 2**, substitua o [link simbólico](https://www.shellhacks.com/symlink-create-symbolic-link-linux/)  de ``docker-compose.yaml`` (que atualmente aponta para ``Resources/docker-compose.dev.yaml``) para ``Resources/docker-compose.extra-services.yaml`` : :computer:

	ln -snf Resources/docker-compose.extra-services.yaml ./docker-compose.yaml

**Passo 3**, levante os Serviços : :computer:

	docker-compose up -d
