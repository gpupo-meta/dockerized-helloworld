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

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LogGeneratorCommand extends AbstractCommand
{
    protected $words = [
        'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit',
        'a', 'ac', 'accumsan', 'ad', 'aenean', 'aliquam', 'aliquet', 'ante',
        'aptent', 'arcu', 'at', 'auctor', 'augue', 'bibendum', 'blandit',
        'class', 'commodo', 'condimentum', 'congue', 'consequat', 'conubia',
        'dapibus', 'diam', 'dictum', 'dictumst', 'dignissim', 'dis', 'donec',
        'dui', 'duis', 'efficitur', 'egestas', 'eget', 'eleifend', 'elementum',
        'facilisi', 'facilisis', 'fames', 'faucibus', 'felis', 'fermentum',
        'habitasse', 'hac', 'hendrerit', 'himenaeos', 'iaculis', 'id',
        'imperdiet', 'in', 'inceptos', 'integer', 'interdum', 'justo',
        'lacinia', 'lacus', 'laoreet', 'lectus', 'leo', 'libero', 'ligula',
        'molestie', 'mollis', 'montes', 'morbi', 'mus', 'nam', 'nascetur',
    ];

    protected function factoryMessage(int $i): AMQPMessage
    {
        shuffle($this->words);
        $data = [
            'channel' => 'CLI',
            'level_name' => 'INFO',
            'level' => 200,
            'message' => vsprintf('%s Estou %s testando %s o envio %s de logs a partir %s de App PHP %s ', $this->words),
            'extra' => ['i' => $i],
            'context' => 'DockerizedHelloworld',
        ];

        return new AMQPMessage(json_encode($data));
    }

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

        while ($i < $qtd) {
            ++$i;
            $message = $this->factoryMessage($i);
            $channel->basic_publish($message, 'monolog');
            $output->writeln(sprintf('Sent message #<info>%s</>...', $i));
        }

        $channel->close();
        $connection->close();
        $output->writeln('Done');
    }
}
