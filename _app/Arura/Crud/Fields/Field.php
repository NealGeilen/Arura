<?php
namespace Arura\Crud\Fields;


use Nette\Forms\Form;

class Field{

    protected $type;
    protected $tag;
    protected $name;
    protected $attributes = [];

    /**
     * Field constructor.
     * @param string $sType
     * @param string $tag
     * @param string $name
     */
    public function __construct($sType = "", $tag ="", $name ="")
    {
        $this->type =$sType;
        $this->tag = $tag;
        $this->name = $name;
    }

    /**
     * get input field for given column
     * @param array $aData
     * @param bool $disable
     * @param bool $required
     * @return string
     */
    public function getInput($aData, Form $form,$disable = false, $required = true){
        $value = (!empty($aData)) ? $aData[$this->getTag()] : null;
        $field = null;
        switch ($this->getType()){
            case "password":
                $field = $form->addPassword($this->getTag(),$this->getName());
                break;
            case "textearea":
                $field = $form->addTextArea($this->getTag(),$this->getName());
                break;
            case "email":
                $field = $form->addEmail($this->getTag(),$this->getName());
                break;
            case "number":
                $field = $form->addInteger($this->getTag(),$this->getName());
                break;
            case "hidden":
                $field = $form->addHidden($this->getTag(),$this->getName());
                break;
            case "text":
            default:
                $field = $form->addText($this->getTag(),$this->getName());
                break;
        }
        foreach ($this->attributes as $key => $value){
            $field->setHtmlAttribute($key, ((!($value)) ? $value: true));
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
    }

    public function addAttribute($key , $value = null){
        $this->attributes[$key] = $value;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    public function getData($sData)
    {
        return $sData;
    }

}
