<?php
namespace Arura;

use Arura\Exceptions\Error;
use Nette\Forms\Controls\Checkbox;

class Form extends \Nette\Forms\Form{

    const TwoColumnRender = self::class . "::TwoColForm";
    const OneColumnRender = self::class . "::OneColForm";

    public function __construct(string $name = null, string $FormRender = self::TwoColumnRender)
    {
        parent::__construct($name);
        $this->addProtection();
        $this->onRender[] = $FormRender;
    }

    public function startForm(){
        $s =  "<form action='{$this->httpRequest->getUrl()->getAbsoluteUrl()}' method='post'>";
        foreach ($this->components as $component){
            if ($component->options["type"] === "hidden"){
                $s .= $component->getControl();
            }
        }
        return $s;
    }

    public function endForm(){
        return "</form>";
    }

    public function getControl($name,string $cssClass = null){
        if (isset($this->components[$name])){
            $item = $this->components[$name];
            $control = $item->getControl();
            $sType = $item->getOptions()["type"];
            $sErrors = "";
            foreach ($item->errors as $sError){
                $sErrors .= "<span class='text-danger'>{$sError }</span>". "<br/>";
            }
            switch ($sType){
                case "button":
                case "submit":
                    $control->setAttribute("class", "btn btn-primary {$cssClass}");
                    break;
                case "reset":
                    $control->setAttribute("class", "btn btn-secondary {$cssClass}");
                    break;
                case "checkbox":
                    return (string)$control . $sErrors;
                    break;
                case "select":
                    $control->setAttribute("value", $item->value);
                default:
                    $control->setAttribute("class", "form-control {$cssClass}");
                    break;
            }
            return "<div class='form-group'>{$item->getLabel()}{$control}{$sErrors}</div>";
        } else {
            throw new Error("Control not found", 500);
        }
    }


    public static function TwoColForm(Form $form): void
    {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = 'div class="row"';
        $renderer->wrappers['pair']['container'] = 'div class="form-group col-md-6 col-12"';
        $renderer->wrappers['pair']['.error'] = 'has-danger';
        $renderer->wrappers['control']['description'] = 'span class=form-text';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
        $renderer->wrappers['control']['.error'] = 'is-invalid';

        self::bootstrap4($form);
    }

    public static function OneColForm(Form $form): void
    {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = 'div class="row"';
        $renderer->wrappers['pair']['container'] = 'div class="form-group col-12"';
        $renderer->wrappers['pair']['.error'] = 'has-danger';
        $renderer->wrappers['control']['description'] = 'span class=form-text';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
        $renderer->wrappers['control']['.error'] = 'is-invalid';
        self::bootstrap4($form);
    }

    protected static function bootstrap4(Form $form){
        foreach ($form->getControls() as $control) {
            $type = $control->getOption('type');
            if ($type === 'button') {
                $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-secondary');
                $usedPrimary = true;

            } elseif (in_array($type, ['text', 'textarea', 'select'], true)) {
                $control->getControlPrototype()->addClass('form-control');

            } elseif ($type === 'file') {
                $control->getControlPrototype()->addClass('form-control-file');

            } elseif (in_array($type, ['checkbox', 'radio'], true)) {
                if ($control instanceof Checkbox) {
                    $control->getLabelPrototype()->addClass('form-check-label');
                } else {
                    $control->getItemLabelPrototype()->addClass('form-check-label');
                }
                $control->getControlPrototype()->addClass('form-check-input');
                $control->getSeparatorPrototype()->setName('div')->addClass('form-check');
            }
        }
    }
}