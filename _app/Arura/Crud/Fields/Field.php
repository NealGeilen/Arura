<?php
namespace Arura\Crud\Fields;


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
        $this->addAttribute("type", $this->getType());
        $this->addAttribute("name", $this->getTag());
        $this->addAttribute("class", "form-control");
    }

    /**
     * get input field for given column
     * @param array $aData
     * @param bool $disable
     * @param bool $required
     * @return string
     */
    public function getInput($aData, $disable = false, $required = true){
        $value = (!empty($aData)) ? $aData[$this->getTag()] : null;
        $this->addAttribute("value", $value);

        if ($required){
            $this->addAttribute("required");
        }
        if ($disable){
            $this->addAttribute("readonly");
        }
        return "<input {$this->getAttributes()}/>";
    }

    protected function getAttributes(){
        $sAttributes = "";
        foreach ($this->attributes as $key => $value){
            $sAttributes.= $key . ((!empty($value)) ? "='{$value}'": null);
        }
        return $sAttributes;
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
