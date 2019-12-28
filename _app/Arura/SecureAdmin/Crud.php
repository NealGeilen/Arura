<?php
namespace Arura\SecureAdmin;
use Arura\User\User;

class Crud extends Database {

    protected $sHtml = "";
    protected $aDataFile;
    protected $aColumnInfo;
    protected $oAdmin;

    public function __construct($aData, $sKey, SecureAdmin $oAdmin){
        $this->aDataFile = $aData;
        $this->oAdmin = $oAdmin;
        parent::__construct($sKey, $this->aDataFile["primarykey"]);
    }
    private function isActionAllowed($iRight){
        return $this->oAdmin->hasUserRight(User::activeUser(), $iRight);
    }
    private function Actions(){
        try{
            switch ($_GET['action']){
                case 'create':
                    if ($this->isActionAllowed(SecureAdmin::CREATE)){
                        $this -> sHtml = $this->buildInputField([],'save') . $this->sHtml;
                    }
                    break;
                case 'save':
                    if ($this->isActionAllowed(SecureAdmin::CREATE)){
                        foreach ($_POST as $sName => $sValue){
                            if (str_contains("auto_increment",$this->aColumnInfo[$sName]['Extra'])){
                                unset($_POST[$sName]);
                            }
                        }
                        $this->createRecord($this->oAdmin->getDbName(), $_POST);
                        header('Location:' . $_SERVER["REDIRECT_URL"] . "?t=" . $this->oAdmin->getId());
                    }
                    break;
                case 'edit':
                    if ($this->isActionAllowed(SecureAdmin::EDIT)){
                        if (isset($_GET['_key'])){
                            $this -> sHtml = $this->buildInputField($this->SelectRow($this->oAdmin->getDbName(), $_GET['_key'],$this->aDataFile["primarykey"]), "update"). $this->sHtml;
                        }
                    }
                    break;
                case 'update':
                    if (isset($_POST[$this->aDataFile["primarykey"]]) && $this->isActionAllowed(SecureAdmin::EDIT)){
                        $this->updateRecord($this->oAdmin->getDbName(), $_POST, $this->aDataFile["primarykey"]);
                        if ($this->isQuerySuccessful()){
                            header('Location:' . $_SERVER["REDIRECT_URL"] . "?t=" . $this->oAdmin->getId());
                        }
                    }
                    break;
                case 'delete':
                    if (isset($_GET['_key']) || $this->isActionAllowed(SecureAdmin::DELETE)){
                        $this->query('DELETE FROM '. $this->oAdmin->getDbName() . ' WHERE ' . $this->aDataFile["primarykey"] . ' = :'.$this->aDataFile["primarykey"], [$this->aDataFile["primarykey"] => $_GET['_key']]);
                        if ($this->isQuerySuccessful()){
                            header('Location:' . $_SERVER["REDIRECT_URL"] . "?t=" . $this->oAdmin->getId());
                        }
                    }
                    break;
            }
        } catch (\Exception $e){
            header('Location:' . $_SERVER["REDIRECT_URL"] . "?t=" . $this->oAdmin->getId());
        }
    }

    /**
     * @return string
     */
    public function getHTMLTable()
    {
        if (isset($_GET["action"])){
            $this->Actions();
        }
        $this->buildTable();
        return $this->sHtml;
    }

    private function buildTable(){
        $aData = $this->SelectAll($this->oAdmin->getDbName(), $this->aDataFile["primarykey"]);
        $this->sHtml .= "<table class='table Arura-Table'>";
        $this->sHtml .= "<thead><tr>";
        foreach ($this->aDataFile["columns"] as $Column){
            $this->sHtml .= "<th>" . $Column['name'] . "</th>";
        }
        $this->sHtml .= "<th>";
        if ($this->isActionAllowed(SecureAdmin::CREATE)){
            if ($_GET["action"] !== "create" && $_GET["action"] !== "edit"){
                $this->sHtml .= "<a class='btn btn-primary btn-sm' href='".$_SERVER["REDIRECT_URL"]."?t=".$this->oAdmin->getId()."&action=create'><i class=\"fas fa-plus\"></i></a>";
            }
        }
        $this->sHtml .= "</th></tr></thead><tbody>";
        foreach ($aData as $record){
            $this->sHtml  .= "<tr>";
            foreach ($record as $tag => $column){
                switch ($this->aDataFile["columns"][$tag]["type"]){
                    case "dropdown":
                        $this->sHtml  .= "<td>" . $this->aDataFile["columns"][$tag]["options"][$column] . "</td>";
                        break;
                    default:
                        $this->sHtml  .= "<td>" . $column . "</td>";
                        break;

                }
            }
            $this->sHtml .= "<td>".$this->getActionButtons($record[$this->aDataFile["primarykey"]])."</td>";
            $this->sHtml  .= "</tr>";

        }
        $this->sHtml  .= "</tbody></table>";
    }

    protected function getActionButtons($iRowId = null){
        $s = "<div class='btn-group btn-group-sm'>";
        if ($this->isActionAllowed(SecureAdmin::DELETE)){
            $s .= "<a href='".$_SERVER["REDIRECT_URL"]."?t=".$this->oAdmin->getId()."&action=delete&_key=".$iRowId."' class='btn btn-danger'><i class=\"fas fa-trash\"></i></a>";
        }
        if ($this->isActionAllowed(SecureAdmin::EDIT)){
            $s .= "<a href='".$_SERVER["REDIRECT_URL"]."?t=".$this->oAdmin->getId()."&action=edit&_key=".$iRowId."' class='btn btn-primary'><i class=\"fas fa-pen\"></i></a>";
        }
        $s .= "</div>";
        return $s;
    }

    protected  function buildInputField($aData = [],$sAction = null){
        $sHtml = "<h2>".(($sAction==="save") ? "Toevoegen" : "Bewerken")."</h2>";
        $sHtml .= "<form method='post' action='".$_SERVER["REDIRECT_URL"]."?t=".$this->oAdmin->getId()."&action=".$sAction."' class='form-row bg-secondary p-1 border-4 border-dark rounded'>";
        foreach ($this->aDataFile["columns"] as $tag => $column){
            $value = null;
            if (isset($aData[$tag])){
                $value = $aData[$tag];
            }
            $sInputGroup = "<div class='form-group col-xl-3 col-md-3 col-sm-6 col-12'>";
            if (!str_contains("auto_increment",$this->aColumnInfo[$tag]["Extra"])){
                $sInputGroup .= "<label>".$column['name']."</label>";
                switch ($column["type"]){
                    case "dropdown":
                        $sInputGroup .= "<select class='form-control' value='".$value."' name='".$tag."'>";
                        if (isset($column["options"])){
                            foreach ($column["options"] as $i => $x){
                                $sInputGroup .= "<option value='".$i."'>" . $x . "</option>";
                            }
                        }
                        $sInputGroup .= "</select>";
                        break;
                    default:
                        $sInputGroup.= "<input type='".$column["type"]."' name='".$tag."' value='".$value."' class='form-control' ".(($sAction === "update" && $tag === $this->aDataFile["primarykey"]) ? "readonly" : null)."/>";
                        break;
                }
                ;

            } else {
                $sInputGroup = "<input type='hidden' name='".$tag."' value='".$value."'/>";
            }
            $sInputGroup .= "</div>";
            $sHtml .= $sInputGroup;
        }

        $sHtml .= "<div class='col-md-12'><div class='btn-group'><input type='submit' class='btn btn-success' value='Opslaan'><a class='btn btn-danger' href='".$_SERVER["REDIRECT_URL"]."?t=".$this->oAdmin->getId()."'>Annuleren</a></div></div>";
        $sHtml .= "";
        $sHtml .= "</form>";
        return $sHtml;
    }

    public function __toString()
    {
        return $this->getHTMLTable();
    }
}