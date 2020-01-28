<?php
namespace Arura\Crud\Fields;

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
        $sHtml = "<select {$this->getAttributes()}>";
        foreach ($this->options as $k => $v){
            $sHtml .= "<option value='".$k."'>" . $v . "</option>";
        }
        $sHtml .= "</select>";
        return $sHtml;
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
