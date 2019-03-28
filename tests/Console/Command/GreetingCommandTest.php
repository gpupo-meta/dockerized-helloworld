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

namespace Gpupo\DockerizedHelloworld\Tests\Console\Command;

use Gpupo\DockerizedHelloworld\Console\Command\GreetingCommand;
use PHPUnit\Framework\TestCase as CoreTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Gpupo\CommonSdk\Factory;

/**
 * @coversDefaultClass \Gpupo\DockerizedHelloworld\Console\Command\GreetingCommand
 */
class GreetingCommandTest extends CoreTestCase
{
    public function dataProviderGreetingCommand()
    {
        $command = new GreetingCommand(new Factory());
        $commandTester = new CommandTester($command);

        return [
            [$command, $commandTester, 'Elvis Aaron Presley'],
            [$command, $commandTester, 'Ron Kenoly'],
            [$command, $commandTester, 'Vietnam'],
        ];
    }

    /**
     * @testdox It has a well-educated ``execute`` method to say hello.
     * @cover ::execute
     * @dataProvider dataProviderGreetingCommand
     */
    public function testExecute(GreetingCommand $command, CommandTester $commandTester, string $name)
    {
        $commandTester->execute([
           'name' => $name,
       ]);

       $output = $commandTester->getDisplay();
       $expected = sprintf('Hello %s', $name);
       $this->assertStringContainsString($expected, $output);
    }
}
