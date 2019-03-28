Muito bem, você zerou o jogo :) :checkered_flag:

Você pode contribuir com este projeto criando uma [Pull Request](https://help.github.com/en/articles/creating-a-pull-request) ou informando o bug/melhoria em [issues](https://github.com/gpupo-meta/dockerized-helloworld/issues). Isto inclui correções ortográficas.

Veja a [lista de melhorias](https://github.com/gpupo-meta/dockerized-helloworld/labels/enhancement) que precisam de desenvolvimento.

### Shutdown

Para desligar todos os container levantados durante a execução deste tutorial, você pode usar este comando: :computer:

	docker stop $(docker ps -a -q)

:memo: Agora um extra, se você configurou seu computador usando o [ gpupo-meta/setup-machine](https://github.com/gpupo-meta/setup-machine), basta executar:  :computer:

	docker-stop-all

Depois de carregar milhões de bits em imagens Docker, talvez você precise liberar removendo todas as imagens Docker em cache: :computer:

	docker-remove-all
