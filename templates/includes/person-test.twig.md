
```PHP
//...
namespace Gpupo\DockerizedHelloworld\Tests\Entity;

use PHPUnit\Framework\TestCase as CoreTestCase;
use Gpupo\DockerizedHelloworld\Entity\Person;

/**
 * @coversDefaultClass \Gpupo\DockerizedHelloworld\Entity\Person
 * ...
 */
class PersonTest extends CoreTestCase
{
    public function dataProviderPerson()
    {
        $expected = [
            "name" => "d1b72da",
        ];
        $object = new Person();

        return [[$object, $expected]];
    }

    /**
     * @testdox Have a getter getName() to get Name
     * @dataProvider dataProviderPerson
     * @cover ::getName
     * @small
     * @test
     *
     * @param Person $person Main Object
     * @param array $expected Fixture data
     */
    public function testGetName(Person $person, array $expected)
    {
        $person->setName($expected['name']);
        $this->assertSame($expected['name'], $person->getName());
    }

    /**
     * @testdox Have a setter setName() to set Name
     * @dataProvider dataProviderPerson
     * @cover ::setName
     * @small
     * @test
     *
     * @param Person $person Main Object
     * @param array $expected Fixture data
     */
    public function testSetName(Person $person, array $expected)
    {
        $person->setName($expected['name']);
        $this->assertSame($expected['name'], $person->getName());
    }
}
```
