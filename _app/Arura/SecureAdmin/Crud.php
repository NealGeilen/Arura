<?php
namespace Arura\SecureAdmin;
use NG\User\User;

class Crud extends Database {

    protected $sHtml;
    protected $aDataFile;
    protected $aColumnInfo;
    protected $oAdmin;

    public function __construct($aData, $sKey, SecureAdmin $oAdmin){
        $this->aDataFile = $aData;
        $this->oAdmin = $oAdmin;
        parent::__construct($sKey);
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
                        if (isset($_POST[$this->aDataFile["primarykey"]])){
                            unset($_POST[$this->aDataFile["primarykey"]]);
                        }
                        foreach ($_POST as $sName => $sValue){
                            if (str_contains("auto_increment",$this->aColumnInfo[$sName]['Extra'])){
                                unset($_POST[$sName]);
                            }
                        }
                        $this->createRecord($this->aDataFile["table"], $_POST);
                    }
                    break;
                case 'edit':
                    if ($this->isActionAllowed(SecureAdmin::EDIT)){
                        if (isset($_GET['_key'])){
                            $this -> sHtml = $this->buildInputField($this->SelectRow($this->aDataFile["table"], $_GET['_key'],$this->aDataFile["primarykey"])). $this->sHtml;
                        }
                    }
                    break;
                case 'update':
                    if (isset($_POST[$this->aDataFile["primarykey"]]) && $this->isActionAllowed(SecureAdmin::EDIT)){
                        $this->updateRecord($this->aDataFile["table"], $_POST, $this->aDataFile["primarykey"]);
                        if ($this->isQuerySuccessful()){
                            header('Location:' . $_SERVER["REDIRECT_URL"]);
                        }
                    }
                    break;
                case 'delete':
                    if (isset($_GET['_key']) || $this->isActionAllowed(SecureAdmin::DELETE)){
                        $this->query('DELETE FROM '. $this->aDataFile["table"] . ' WHERE ' . $this->aDataFile["primarykey"] . ' = :'.$this->aDataFile["primarykey"], [$this->aDataFile["primarykey"] => $_GET['_key']]);
                    }
                    break;
            }
        } catch (\Exception $e){
            \header('Location:' . $_SERVER["REDIRECT_URL"]);
        }
    }

    /**
     * @return string
     */
    public function getHTMLTable()
    {
        return $this->sHtml;
    }

    private function buildTable(){
        $sQuery = "SELECT ";
        foreach ($this->aDataFile["columns"] as $tag => $aData){
            $sQuery .= $tag . ", ";
        }
        $sQuery = trim($sQuery,", ");

        $sQuery .= " FROM " . $this->aDataFile["table"];
        $aData = $this->fetchAll($sQuery);
        if ($this->isActionAllowed(SecureAdmin::CREATE)){
            $this->sHtml .= "<a href='".$_SERVER["REDIRECT_URL"]."?action=create'>Toevoegen</a>";
        }
        $this->sHtml .= "<table class='table'>";
        $this->sHtml .= "<tr>";
        foreach ($this->aDataFile["columns"] as $Column){
            $this->sHtml .= "<th>" . $Column['name'] . "</th>";
        }
        $this->sHtml .= "</tr>";
        foreach ($aData as $record){
            $this->sHtml  .= "<tr>";
            foreach ($record as $column){
                $this->sHtml  .= "<td>" . $this->decrypt($column) . "</td>";;
            }
            $this->sHtml .= "<td>".$this->getActionButtons($record[$this->aDataFile["primarykey"]])."</td>";
            $this->sHtml  .= "</tr>";

        }
        $this->sHtml  .= "</table>";
    }

    protected function getActionButtons($iRowId = null){
        $s = "";
        if ($this->isActionAllowed(SecureAdmin::DELETE)){
            $s .= "<a href='".$_SERVER["REDIRECT_URL"]."?action=delete&_key=".$iRowId."' class='btn btn-primary'>Verwijderen</a>";
        }
        if ($this->isActionAllowed(SecureAdmin::EDIT)){
            $s .= "<a href='".$_SERVER["REDIRECT_URL"]."?action=edit&_key=".$iRowId."' class='btn btn-primary'>Veranderen</a>";
        }
        return $s;
    }

    protected  function buildInputField($aData = [],$sAction = null){
        $sHtml = "<form method='post' action='".$_SERVER["REDIRECT_URL"]."?action=".$sAction."'><table>";
        foreach ($this->aDataFile["columns"] as $tag => $column){
            $value = "";
            $sInputGroup = "<tr>";
            if (!empty($aData)){
                $value = $aData[$tag];
            }
            if (!str_contains("auto_increment",$this->aColumnInfo[$tag]["Extra"])){
                $sInputGroup .= "<td><label>".$column['name']."</label></td>";
                $sInputGroup.= "<td><input type='".$column["type"]."' name='".$tag."' value='".$value."'/></td>";
            } else {
                $sInputGroup = "<td><input type='hidden' name='".$tag."' value='".$value."'/></td>";
            }
            $sInputGroup .= "</tr>";
            $sHtml .= $sInputGroup;
        }

        $sHtml .= "<tr><td><input type='submit'></td><td><input type='reset'></td></tr>";
        $sHtml .= "";
        $sHtml .= "</table></form>";
        return $sHtml;
    }

    public function __toString()
    {
        return $this->getHTMLTable();
    }
}