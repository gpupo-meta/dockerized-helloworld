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

final class GreetingCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('greeting')
            ->setDescription('Saudação')
            ->addArgument('name', InputArgument::OPTIONAL, 'Seu nome', 'World')
            ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Hello <info>%s</>!', $input->getArgument('name')));
    }
}
