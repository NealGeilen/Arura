<?php
namespace Arura\Crud;

use Crud\Fields\Field;
use Nette\Forms\Controls\Checkbox;

class Form {

    protected $oCrud;
    public $aData;

    protected $sSaveBtn = "Opslaan";
    protected $sCancelBtn = "Annuleren";

    /**
     * Form constructor.
     * @param Crud $oCrud\
     */
    public function __construct(Crud $oCrud){
        $this->oCrud = $oCrud;
    }

    /**
     * Get all input groups
     * @return \Nette\Forms\Form
     */
    protected function getInputGroups(){
        $form = new \Nette\Forms\Form();
        $form->onRender[] = self::class . '::makeBootstrap4';
        foreach ($this->oCrud->getFields() as $oField){
            $this->getInputGroup($oField, $form);
        }
        return $form;
    }

    /**
     * Build one input group
     * @param Field $oField
     * @return \Nette\Forms\Form
     */
    protected function getInputGroup(Field $oField, \Nette\Forms\Form $form){
        if (!Crud::str_contains("auto_increment",$this->oCrud->aColumnData[$oField->getTag()]["Extra"])){
            $oField->getInput($this->aData, $form, ($oField->getTag() === $this->oCrud->sPrimaryKey && $this->oCrud->sActions === Actions::EDIT), $this->oCrud->aColumnData[$oField->getTag()]["Null"] !== "YES");
        } else {
            $value = (!empty($this->aData)) ? $this->aData[$oField->getTag()] : null;
            $form->addHidden($oField->getTag(), $value);
        }
        return  $form;
    }

    /**
     * Build form HTML
     * @return \Nette\Forms\Form
     * @throws Exceptions\CrudException
     */
    public function getForm()
    {
        $sPrimaryKey = null;
        $form = $this->getInputGroups();
        if ($this->oCrud->sActions === Actions::EDIT){
            $this->collectRecordData();
            $form->setAction($this->oCrud->getUrl(["action" => "update"]));
            $form->setDefaults($this->aData);
            $sPrimaryKey =  $this->aData[$this->oCrud->sPrimaryKey];
        } else {
            $form->setAction($this->oCrud->getUrl(["action" => "save"]));
        }

        $form->addProtection();

        $form->addHidden($this->oCrud->sPrimaryKey, $sPrimaryKey);
        $form->addSubmit("_save", $this->sSaveBtn)->setHtmlAttribute("class", "btn btn-primary");
        $form->addButton("_cancel", $this->sCancelBtn)->setHtmlAttribute("class", "btn btn-danger")->setHtmlAttribute("onclick", "location.replace('{$this->oCrud->getUrl()}')");
        return $form;
    }

    /**
     * Collect data form single selected record
     * @throws Exceptions\CrudException
     */
    protected function collectRecordData(){
        $this->aData = $this->oCrud->db->fetchRow("SELECT * FROM {$this->oCrud->sTable} WHERE {$this->oCrud->sPrimaryKey} = :{$this->oCrud->sPrimaryKey}", [$this->oCrud->sPrimaryKey => $_GET["_key"]]);
    }

    /**
     * @return string
     * @throws Exceptions\CrudException
     */
    public function __toString()
    {
        if ($this->oCrud->sActions === Actions::EDIT || $this->oCrud->sActions === Actions::CREATE){
            return (string)$this->getForm();
        }
        return "";
    }

    public static function makeBootstrap4(\Nette\Forms\Form $form): void
    {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = 'div class="row"';
        $renderer->wrappers['pair']['container'] = 'div class="form-group col-md-6 col-12"';
        $renderer->wrappers['pair']['.error'] = 'has-danger';
        $renderer->wrappers['control']['description'] = 'span class=form-text';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
        $renderer->wrappers['control']['.error'] = 'is-invalid';

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

    /**
     * @param string $sSaveBtn
     */
    public function setSaveBtn(string $sSaveBtn)
    {
        $this->sSaveBtn = $sSaveBtn;
    }

    /**
     * @param string $sCancelBtn
     */
    public function setCancelBtn(string $sCancelBtn)
    {
        $this->sCancelBtn = $sCancelBtn;
    }
}
