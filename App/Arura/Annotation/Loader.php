<?php
namespace Arura\Annotation;


use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class Loader{

    /**
     * @author Kevin Sentjens
     */
    const REGEX_BLOCK = '/[*\s]* 
(?P<annotation_name>[A-Z][\w\\\\\\\\\\\\\\\\]+) 
\("
(?P<first>
[\/\w{}\[\]\^+\(\)\\\\-]+
)?
"
  (?P<value> 
    (?:
      [^@]*
      [^*\s)] 
    )
  )? 
(?:\s|\n|\))/sxmu';

    /**
     * @author Kevin Sentjens
     */
    const REGEX_OPTIONS = '/(?P<key>[a-zA-Z]+)\="(?P<value>[a-zA-Z]+)?(?:["])/';

    protected $reflection;

    /**
     * Loader constructor.
     * @param string $class
     * @throws ReflectionException
     */
    public function __construct(string $class)
    {
        $this->reflection = new ReflectionClass($class);
    }


    /**
     * @return Method[]
     */
    public function load(){
        $a = [];
        foreach ($this->reflection->getMethods() as $method){
            $a[] = $this->loadMethod($method);
        }
        return $a;
    }

    /**
     * @param ReflectionMethod $method
     * @return Method[]
     */
    public function loadMethod(ReflectionMethod $method){
        preg_match_all(self::REGEX_BLOCK, $method->getDocComment(), $matches, PREG_SET_ORDER, 0);
        $aMethods = [];
        foreach ($matches as $match){
            $Method = new Method($match["annotation_name"], $match["first"], $method);
            if (isset($match["value"])){
                preg_match_all(self::REGEX_OPTIONS, $match["value"], $options, PREG_SET_ORDER, 0);
                foreach ($options as $aOption){
                    $Method->addOption(new Option($aOption["key"], $aOption["value"]));
                }
            }
            $aMethods[$Method->getName()] = $Method;

        }
        return $aMethods;
    }
}