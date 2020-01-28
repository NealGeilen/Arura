<?php
namespace Arura\Crud;

use Arura\Crud\Fields\Field;
use Arura\Exceptions\Error;

class Form {

    protected $oCrud;
    public $aData;

    /**
     * Form constructor.
     * @param Crud $oCrud\
     */
    public function __construct(Crud $oCrud){
        $this->oCrud = $oCrud;
    }

    /**
     * Get all input groups
     * @return string
     */
    protected function getInputGroups(){
        $sHtml = "";
        foreach ($this->oCrud->getFields() as $oField){
            $sHtml .= $this->getInputGroup($oField);
        }
        return $sHtml;
    }

    /**
     * Build one input group
     * @param Field $oField
     * @return string
     */
    protected function getInputGroup(Field $oField){
        $sInputGroup= "";
        if (!str_contains("auto_increment",$this->oCrud->aColumnData[$oField->getTag()]["Extra"])){
            $sInput = $oField->getInput($this->aData, ($oField->getTag() === $this->oCrud->sPrimaryKey && $this->oCrud->sActions === Actions::EDIT), $this->oCrud->aColumnData[$oField->getTag()]["Null"] !== "YES");
            if (!empty($sInput)){
                $sInputGroup .= "<div class='form-group col-xl-3 col-md-3 col-sm-6 col-12'>";
                $sInputGroup .= "<label>{$oField->getName()}</label>";
                $sInputGroup .= $sInput;
                $sInputGroup .= "</div>";
            }
        } else {
            $value = (!empty($this->aData)) ? $this->aData[$oField->getTag()] : null;
            $sInputGroup .="<input type='hidden' name='{$oField->getTag()}' value='{$value}'>";
        }

        return $sInputGroup;
    }

    /**
     * Build form HTML
     * @return string
     * @throws Error
     */
    public function getForm()
    {
        $sPrimaryKey = null;
        $aPost = [
            Actions::EDIT => Actions::UPDATE,
            Actions::CREATE => Actions::SAVE
        ];
        if ($this->oCrud->sActions === Actions::EDIT){
            $this->collectRecordData();
            $sPrimaryKey =  $this->aData[$this->oCrud->sPrimaryKey];
        }
        $sName  = ($this->oCrud->sActions === Actions::CREATE) ? "Toevoegen" : "Bewerken";
        $sHtml = "<form method='post' action='{$this->oCrud->getUrl(["action" => $aPost[$this->oCrud->sActions]])}' class='bg-dark p-3 border-4 border-dark'>";
        $sHtml .= "<h2>{$sName}</h2>";
        $sHtml .= "<input type='hidden' value='{$sPrimaryKey}' name='{$this->oCrud->sPrimaryKey}'>";
        $sHtml .= "<div class='form-row'>";
        $sHtml .= $this->getInputGroups();
        $sHtml .= "</div>";
        $sHtml .= "<div class='btn-group'><input type='submit' class='btn btn-success' value='Opslaan'><a class='btn btn-danger' href='{$this->oCrud->getUrl()}'>Annuleren</a></div>";
        $sHtml .= "</form>";
        return $sHtml;
    }

    /**
     * Collect data form single selected record
     * @throws Error
     */
    protected function collectRecordData(){
        $this->aData = $this->oCrud->db->fetchRow("SELECT * FROM {$this->oCrud->sTable} WHERE {$this->oCrud->sPrimaryKey} = :{$this->oCrud->sPrimaryKey}", [$this->oCrud->sPrimaryKey => $_GET["_key"]]);

    }

    /**
     * @return string
     * @throws Error
     */
    public function __toString()
    {
        return $this->getForm();
    }
}
