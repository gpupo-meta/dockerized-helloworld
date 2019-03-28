Os exemplos abaixo são escritos para execução em um terminal linux, mas você pode facilmente executá-los em outro sistema operacional com alguns ajustes.

Este projeto considera que você já possui o [Docker](https://docs.docker.com/release-notes/docker-ce/) e o [Docker Compose](https://docs.docker.com/compose/install/) instalado em seu sistema operacional(veja [ gpupo-meta/setup-machine](https://github.com/gpupo-meta/setup-machine)). Se você possui um computador sem suporte a virtualização talvéz não consiga rodar o Docker. Eu enfrentei este problema em um Mac Book Pro 2010.

Se você pretende seguir as instruções abaixo até o fim, prepare-se para trafegar mais de 3Gb de dados, entre imagens Docker e pacotes de dependência, então, se você está dependendo de sua conexão EDGE, apenas leia o conteúdo, a leitura recomendada e deixa pra executar pra valer quando estiver melhor de conexão, ok?

Vários termos usados neste tutorial possuem links que facilitarão o entendimento de quem não está familiarizado com eles, então recomendo a leitura das referências.

Alguns comandos devem ser executados em seu terminal tradicional e quando for este o caso, o símbolo :computer: estará presente, porém outros comandos requerem a execução a partir do terminal virtualizado. Quando for este o caso, o símbolo :whale: estará próximo, indicando que a execução deve ser feita no bash do container. Como chegar lá ? Você vai aprender logo abaixo...

Um último requisito importante é **paciência** e **dedicação** pois é bastante coisa pra ler, seguir referências, executar comandos, analizar diffs e refazer até entender. Pra te motivar e também responsabilizar, eu gastei várias horas de trabalho escrevendo este tutorial, tirando as melhores técnicas do meu vaú de tesouross, para que você aí do futuro aprendesse a usá-las, então, me dê algum crédito e esforço quando seguir com este tutorial, ou, se preferir algo mais facil, por [seguir por aqui](http://bfy.tw/Mw0J) ...

Se tudo estiver pronto, selecione seu personagem e vamos em frente.
