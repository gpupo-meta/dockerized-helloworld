Uma ferramenta muito importante é o [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) que vai alinhar o código escrito de acordo com as regras de padrão selecionados para o projeto.

No arquivo [.php_cs.dist](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/.php_cs.dist) é configurado este conjunto de regras.

Vamos a um exemplo prático! Apesar de funcionar, o arquivo [src/Traits/VeryWrongCodeStyleTrait.php](https://github.com/gpupo-meta/dockerized-helloworld/blob/master/src/Traits/VeryWrongCodeStyleTrait.php) está mal escrito e ignora vários padrões de escrita. Mas que padrões são estes?
Rode o [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer): :whale:

	make php-cs-fixer

Se você executar um ``git diff`` verá algo assim:

{% include 'templates/includes/phpcsfixer-diff.twig.md' %}

Nesse diff que o arquivo recebeu modificações:

* Adicionou a declaração ``declare(strict_types=1);``.
* Adicionou o *HEADER* padrão a todos os arquivos PHP do projeto.
* Organizou em ordem alfabética as declarações de uso.
* Escreveu com um ``use`` por linha, como pede o CS configurado.
* Removeu o ``use PDO`` pois a classe PDO não recebe nenhum uso nas linhas do arquivo.
* Trocou as ``{`` de lugar, de acordo com o codding style definido.
* Adicionou ponto final a linhas de documentação.

:memo: É uma boa prática você utilizar o ``make php-cs-fixer`` após terminar o desenvolvimento de uma feature PHP.

Para voltar a classe ao seu estado anterior e lhe permitir aproveitar melhor este tutorial:

	git checkout src/Traits/VeryWrongCodeStyleTrait.php
