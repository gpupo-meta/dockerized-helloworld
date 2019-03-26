<?php

namespace Gpupo\DockerizedHelloworld\Traits;

use JMS\Serializer\Annotation as JMS,
    Doctrine\ODM\MongoDB\Mapping\Annotations as ODM,
    PDO;

/**
 * Very wrong code style
 *
 *
 *
 */
trait VeryWrongCodeStyleTrait {

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Assert\Type(type="string")
     * @Assert\NotNull()
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $name;

    /**
     * Set name
     *
     * @param  string $name
     * @return mixed
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}
