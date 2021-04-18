<?php
namespace Arura\Annotation;


class Option{

    protected $name = "";
    protected $value = "";

    public function __construct(string $name, string $value)
    {
        $this->setValue($value);
        $this->setName($name);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


}