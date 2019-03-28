A partir deste ponto, a exploração de uma stack tradicional como a LAMP já ficou para trás.
A seguir temos incrementos que lidam com a forma de trabalho usando a imagem [gpupo/container-orchestration:symfony-dev](https://hub.docker.com/r/gpupo/container-orchestration/tags) e outras ferramentas [opensource.gpupo.com](https://opensource.gpupo.com).

### Yarn/NPM/NodeJS

Para a gestão de dependências CSS/Javascript utilizamos o YARN que já está devidamente instalado e configurado na imagem gpupo/container-orchestration:symfony-dev utilizada no service PHP-FPM do Stack de desenvolvimento.

Assim como o comando ``composer install`` instala os pacotes **PHP** definidos em ``compose.json``, o comando ``yarn install`` instala os pacotes **NPM** definidos em ``package.json``: :whale:

	yarn install

Existindo a necessidade de acrescentar um pacote ao projeto, consultamos https://www.npmjs.com/  ou https://yarnpkg.com para encontrar o identificador do pacote. Exemplo: ``babel-plugin-transform-es2015-parameters`` :whale:.

	yarn add babel-plugin-transform-es2015-parameters --dev

O exemplo acima adiciona um pacote que é carregado apenas no ambiente de desenvolvimento já que utilizamos o parâmetro ``--dev``.

#### Compilando (build)

A partir das instruções de ``assets/js/helloworld.js`` será compilado o arquivo ``public/build/helloworld.min.js`` : :whale:

	yarn build

Podemos testar o resultado da seguinte maneira : :whale:

	nodejs public/build/helloworld.min.js

### Babel/ES2015

Uma escrita moderna de código javascript utiliza ``ES6`` também conhecido como ``ECMAScript 6`` ou ``ES2015``.
Aqui entra o [Babel](https://babeljs.io/), um compilador Javascript que nos permite utilizar uma série de recursos ``ES6``.
Não vou detalhar o uso do ``ES6`` aqui nesse documento mas logo abaixo seguem links para o aprendizado da sintaxe.
Em nosso projetto ``dockerized-helloworld`` todas as ferramentas necessárias para compilar javascript ``ES6`` forma instaladas quando você executou ``yarn install``.

O javascript ``assets/js/helloworld-ES2015.js`` foi compilado pelo ``yarn build`` em ``public/build/helloworld-ES2015.min.js``

Podemos testar o resultado da seguinte maneira : :whale:

	nodejs public/build/helloworld-ES2015.min.js

Claro, para que tudo funcionasse foi preciso algumas configurações nos arquivos ``.babelrc`` (instruções para compilação), ``package.json`` (quais pacotes NPM instalar) e ``webpack.config.js`` (quais arquivos compilar e onde fazer o output) e não vou abordar a configuração mas vou deixar links em *leitura recomendada* que tratam disso.

### SASS

O [Sass](https://sass-lang.com/) é uma linguagem baseada em CSS que depois de compilada gera o tradicional CSS.

O arquivo ``assets/scss/app.scss`` inclui todo o css do Bootstrap 4 (disponível na configugação de pacotes e instalados pelo ``yarn install``) e algum código de exemplo que é compilado no path ``public/build/app.min.css``.

### Webpack

A mágica de ``yarn build`` acontece porque o [webpack](https://webpack.js.org/) compila e minimiza nossos arquivos javascript e sass. Mais do que isso, ele recebe a indicação de que o arquivo ``assets/scss/app.scss`` está sendo requerido por ``assets/js/app.js`` e o inclui no processo de build.

![Webpack flow image](https://webpack.github.io/assets/what-is-webpack.png)

Sua configuração é feita a partir do arquivo ``webpack.config.js``.

Você pode acionar o webpack diretamente da seguinte maneira : :whale:

	export PATH="$(yarn bin):$PATH";
	webpack --config webpack.config.js;

Isto é muito útil para testarmos novas configurações.

Para visualizar uma página que carrega o javascript e o css compilado, abra http://dockerized-helloworld.localhost/bootstrap.php .

### Mais leitura recomendada

{% include 'templates/includes/more-ref.twig.md' %}
