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

/**
 * @coversDefaultClass \Gpupo\DockerizedHelloworld\Console\Command\GreetingCommand
 */
class GreetingCommandTest extends CoreTestCase
{
    /**
     * @return GreetingCommand
     */
    public function dataProviderGreetingCommand()
    {
        $expected = [
                ];
        $object = new GreetingCommand();

        return [[$object, $expected]];
    }

    /**
     * @testdox Have a method ``configure()`` .
     * @cover ::configure
     * @dataProvider dataProviderGreetingCommand
     *
     * @param GreetingCommand $greetingCommand Main Object
     * @param array           $expected        Fixture data
     */
    public function testConfigure(GreetingCommand $greetingCommand, array $expected)
    {
        $this->markTestIncomplete('configure() incomplete!');
    }

    /**
     * @testdox Have a method ``execute()`` .
     * @cover ::execute
     * @dataProvider dataProviderGreetingCommand
     *
     * @param GreetingCommand $greetingCommand Main Object
     * @param array           $expected        Fixture data
     */
    public function testExecute(GreetingCommand $greetingCommand, array $expected)
    {
        $this->markTestIncomplete('execute() incomplete!');
    }
}
