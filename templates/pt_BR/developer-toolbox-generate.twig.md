Nosso [projeto dockerized-helloworld](https://github.com/gpupo-meta/dockerized-helloworld) usa a [gpupo/common](https://opensource.gpupo.com/common/) que contém o comando ``vendor/bin/developer-toolbox`` que pode nos ajudar a criar um teste unitário esqueleto a partir de uma classe existente.

Se quisermos criar um teste unitário para o objeto ``Gpupo\DockerizedHelloworld\Entity\Person``: :whale:

	vendor/bin/developer-toolbox generate --class='Gpupo\DockerizedHelloworld\Entity\Person'

O comando acima gerará o arquivo ``tests/Entity/PersonTest.php`` que deve conter um conteúdo semelhante a este:

{% include 'templates/includes/person-test.twig.md' %}


Execute os testes Unitários: :whale:

	make phpunit

Lembre-se de que o arquivo ``tests/Entity/PersonTest.php`` é um rascunho inicial
 e você precisa continuar seu desenvolvimento para transformá-lo em um teste de qualidade.

Quando você tentar editar o arquivo ``tests/Entity/PersonTest.php`` em seu IDE, não conseguirá gravar suas alterações, o que nos leva para a próxima fase ...
