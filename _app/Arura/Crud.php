<?php
namespace Arura;

use NG\Database;

class Crud extends Database {

    private $aDataFile;

    private $aColumnInfo;

    private $sHtml = "";

    public function __construct(
        $sDataFile
    )
    {
        parent::__construct();
        $this->aDataFile = json_array_decode(file_get_contents($sDataFile));
        foreach ($this->fetchAll('SHOW columns FROM '. $this->aDataFile["table"]) as $column){
            $this -> aColumnInfo[$column["Field"]] = $column;
        }
        if (isset($_GET['action'])){
            $this->Actions();
        }
        $this->buildTable();
    }

    private function Actions(){
        try{
            switch ($_GET['action']){
                case 'create':
                    $this -> sHtml = $this->buildInputField([],'save') . $this->sHtml;
                    break;
                case 'save':
                    if (isset($_POST[$this->aDataFile["primarykey"]])){
                        unset($_POST[$this->aDataFile["primarykey"]]);
                    }
                    foreach ($_POST as $sName => $sValue){
                        if (str_contains("auto_increment",$this->aColumnInfo[$sName]['Extra'])){
                            unset($_POST[$sName]);
                        }
                    }
                    $this->createRecord($this->aDataFile["table"], $_POST);
                    break;
                case 'edit':
                    if (isset($_GET['_key'])){
                        $this -> sHtml = $this->buildInputField($this->fetchRow('SELECT * FROM ' . $this->aDataFile["table"] . ' WHERE ' . $this->aDataFile["primarykey"] ." = :". $this->aDataFile["primarykey"], [$this->aDataFile["primarykey"] => $_GET['_key']]),'update') . $this->sHtml;
                    }
                    break;
                case 'update':
                    if (isset($_POST[$this->aDataFile["primarykey"]])){
                        $this->updateRecord($this->aDataFile["table"], $_POST, $this->aDataFile["primarykey"]);
                        if ($this->isQuerySuccessful()){
                            header('Location:' . $_SERVER["REDIRECT_URL"]);
                        }
                    }
                    break;
                case 'delete':
                    if (isset($_GET['_key'])){
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
        $this->sHtml .= "<a href='".$_SERVER["REDIRECT_URL"]."?action=create'>Toevoegen</a>";
        $this->sHtml .= "<table class='table'>";
        $this->sHtml .= "<tr>";
        foreach ($this->aDataFile["columns"] as $Column){
            $this->sHtml .= "<th>" . $Column['name'] . "</th>";
        }
        $this->sHtml .= "</tr>";
        foreach ($aData as $record){
            $this->sHtml  .= "<tr>";
            foreach ($record as $column){
                $this->sHtml  .= "<td>" . $column . "</td>";;
            }
            $this->sHtml .= "<td>".$this->getActionButtons($record[$this->aDataFile["primarykey"]])."</td>";
            $this->sHtml  .= "</tr>";

        }
        $this->sHtml  .= "</table>";
    }

    protected function getActionButtons($iRowId = null){
        $s = "";
        $aButtons = [
            "Verwijder"     => "delete",
            "Veranderen"    => "edit"
        ];
        foreach ($aButtons as $name =>  $aButton){
            $s .= "<a href='".$_SERVER["REDIRECT_URL"]."?action=".$aButton."&_key=".$iRowId."' class='btn btn-primary'>".$name."</a>";
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

//    public static function Build($sTable,$sDatabase = null,$sHost = null, $sUser = null,$sPassword = null){
//        $Dump = new self($sTable,$sDatabase,$sHost,$sUser,$sPassword);
//        $Dump -> buildTable();
//        return $Dump;
//    }

}