<?php
namespace Arura\Annotation;


use ReflectionMethod;

class Method{

    protected $name = "";
    protected $value = "";
    protected $reflectionMethod = null;
    protected $options = [];

    /**
     * Method constructor.
     * @param string $name
     * @param string $value
     * @param ReflectionMethod $reflectionMethod
     */
    public function __construct(string $name, string $value, ReflectionMethod $reflectionMethod)
    {
        $this->setName($name);
        $this->setValue($value);
        $this->setReflectionMethod($reflectionMethod);
    }

    /**
     * @return Option[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Option[] $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function addOption(Option $option){
        $this->options[] = $option;
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

    /**
     * @return ReflectionMethod
     */
    public function getReflectionMethod()
    {
        return $this->reflectionMethod;
    }

    /**
     * @param ReflectionMethod $reflectionMethod
     */
    public function setReflectionMethod($reflectionMethod)
    {
        $this->reflectionMethod = $reflectionMethod;
    }

}