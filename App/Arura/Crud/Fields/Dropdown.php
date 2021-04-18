<?php
namespace Arura\Crud\Fields;

use Nette\Forms\Form;

class Dropdown extends Field{

    protected $options = [];

    /**
     * Dropdown constructor.
     * @param string $tag
     * @param string $name
     */
    public function __construct($tag = "", $name = "")
    {
        parent::__construct("dropdown", $tag, $name);
    }

    /**
     * a Array can be given for options types
     * or an callable for more functions required to return an array
     * @param callable|array $options
     */
    public function setOptions($options){
        if (is_array($options)){
            $this->options=$options;
        } elseif (is_callable($options)){
            foreach ($options() as $key => $value){
                $this->options[$value["K"]] = $value["V"];
            }
        }
    }

    /**
     * get input field for given column
     * @param array $aData
     * @param bool $disable
     * @param bool $required
     * @return object
     */
    public function getInput($aData, Form $form,$disable = false, $required = true){
        $value = (!empty($aData)) ? $aData[$this->getTag()] : null;
        $this->addAttribute("value", $value);
        $field = $form->addSelect($this->getTag(), $this->getName(), $this->options);
        foreach ($this->attributes as $key => $value){
            $field->setHtmlAttribute($key, ((!is_null($value)) ? $value: null));
        }
        if (!isset($this->attributes["placeholder"])){
            $field->setHtmlAttribute("placeholder", $this->getName());
        }
        if ($required){
            $field->setRequired(true);
        }
        if ($disable){
            $field->setHtmlAttribute("readonly");
        }
        $field->setValue($value);

        return $field;
    }

    /**
     * @param string $sData
     * @return string
     */
    public function getData($sData)
    {
        if (isset($this->options[$sData])){
            return $this->options[$sData];
        }
        return "NIET BESCHIKBAAR";
    }
}
