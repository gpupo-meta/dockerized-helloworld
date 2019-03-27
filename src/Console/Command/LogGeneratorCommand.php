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
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

final class LogGeneratorCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('log:generator')
            ->setDescription('Cria e envia logs para a stack RELK')
            ->addArgument('qtd', InputArgument::OPTIONAL, 'Quantidade de registros', 10)
            ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $qtd = (int) $input->getArgument('qtd');
        $i = 0;

        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'php.daemon', '9cs88Hd3jjf', 'logstash');
        $channel = $connection->channel();
        // $channel->exchange_declare('monolog', 'fanout', false, false, false);

        $msg = new AMQPMessage("info: Hello World!");
        $channel->basic_publish($msg, 'logs');

        while ($i < $qtd) {
            ++$i;
            $msg = new AMQPMessage(sprintf("info: Hello World #%d!", $i));
            $channel->basic_publish($msg, 'logs');
            $output->writeln(sprintf('Sent message #<info>%s</>!', $i));
        }

        $channel->close();
        $connection->close();
        $output->writeln('Done');
    }
}
