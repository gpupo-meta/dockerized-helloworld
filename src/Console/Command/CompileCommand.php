<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/dockerized-helloworld
 * Created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file
 * LICENSE which is distributed with this source code.
 * Para a informação dos direitos autorais e de licença você deve ler o arquivo
 * LICENSE que é distribuído com este código-fonte.
 * Para obtener la información de los derechos de autor y la licencia debe leer
 * el archivo LICENSE que se distribuye con el código fuente.
 * For more information, see <https://opensource.gpupo.com/>.
 *
 */

namespace Gpupo\DockerizedHelloworld\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CompileCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('compile')
            ->setDescription('Compila Markdown')
            ->addArgument('locale', InputArgument::OPTIONAL, 'locale', 'pt_BR')
            ;

        parent::configure();
    }

    protected function render($data, $template)
    {
        $loader = new \Twig_Loader_Filesystem(getenv('PWD'));
        $twig = new \Twig_Environment($loader, []);

        return $twig->render($template, $data);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locale = $input->getArgument('locale');
        $base = sprintf('%s.md', $locale);
        $content = $this->render([], sprintf('templates/%s.twig.md', $locale));
        file_put_contents(sprintf('%s', $base), $content);
        $filename = sprintf('docs/%s', $base);
        $jkmd = sprintf("---\nlayout: default\npermalink: /%s.html\n---\n%s", $locale, $content);
        file_put_contents($filename, $jkmd);
        $output->writeln(sprintf('Saved <info>%s</>!', $filename));
    }
}
