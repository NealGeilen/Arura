<?php
namespace Arura;

use Exception;

class Crud extends Database {

    protected $sHtml = "";
    protected $aDataFile;
    protected $aColumnInfo;
    protected $aParas;
    protected $aDefaultValues;
    protected $sCssId;

    /**
     * Crud constructor.
     * @param $sData
     * @param array $aParas
     * @param array $aDefaultValues
     * @param null $sCssId
     * @throws Exceptions\Error
     */
    public function __construct($sData,$aParas = [], $aDefaultValues = [], $sCssId = null){
        $this->aDataFile = json_array_decode(file_get_contents($sData));
        $this->aParas = $aParas;
        $this->aDefaultValues = $aDefaultValues;
        $this->sCssId = $sCssId;
        foreach ($this->fetchAll('SHOW columns FROM '. $this->aDataFile["table"]) as $column){
            $this -> aColumnInfo[$column["Field"]] = $column;
        }
        parent::__construct();
    }

    /**
     * @param array $aParams
     * @return string
     */
    protected function getUrl($aParams = []){
        return $_SERVER["REDIRECT_URL"] ."?". http_build_query(array_merge($aParams, $this->aParas)) . "#" . $this->sCssId;
    }

    /**
     *
     */
    private function Actions(){
        try{
            switch ($_GET['action']){
                case 'create':
                    $this -> sHtml = $this->buildInputField([],'save') . $this->sHtml;
                    break;
                case 'save':
                    foreach ($_POST as $sName => $sValue){
                        if (str_contains("auto_increment",$this->aColumnInfo[$sName]['Extra'])){
                            unset($_POST[$sName]);
                        }
                    }
                    $this->createRecord($this->aDataFile["table"], array_merge($this->aDefaultValues, $_POST));
                    header('Location:' . $this->getUrl());
                    break;
                case 'edit':
                    if (isset($_GET['_key'])){
                        $this -> sHtml = $this->buildInputField($this->fetchRow("SELECT * FROM ". $this->aDataFile["table"] . " WHERE " . $this->aDataFile["primarykey"] ." = :" . $this->aDataFile["primarykey"], [$this->aDataFile["primarykey"] => $_GET["KEY"]]), "update"). $this->sHtml;
                    }
                    break;
                case 'update':
                    $this->updateRecord($this->aDataFile["table"], array_merge($this->aDefaultValues, $_POST), $this->aDataFile["primarykey"]);
                    if ($this->isQuerySuccessful()){
                        header('Location:' . $this->getUrl());
                    }
                    break;
                case 'delete':
                    $this->query('DELETE FROM '. $this->aDataFile["table"] . ' WHERE ' . $this->aDataFile["primarykey"] . ' = :'.$this->aDataFile["primarykey"], [$this->aDataFile["primarykey"] => $_GET['_key']]);
                    if ($this->isQuerySuccessful()){
                        header('Location:' . $this->getUrl());
                    }
                    break;
            }
        } catch (Exception $e){
            header('Location:' . $this->getUrl());
        }
    }

    /**
     * @return string
     * @throws Exceptions\Error
     */
    public function getHTMLTable()
    {
        if (isset($_GET["action"])){
            $this->Actions();
        }
        $this->buildTable();
        return $this->sHtml;
    }

    /**
     * @throws Exceptions\Error
     */
    private function buildTable(){
        $sQuery = "SELECT * FROM ". $this->aDataFile["table"];
        if (!empty($this->aDefaultValues)){
            $sQuery .= " WHERE ";
        }
        foreach ($this->aDefaultValues as $key => $value){
            $sQuery .= $key . ' = :' . $key. ', ';
        }
        $sQuery = trim($sQuery,", ");
        $aData = $this->fetchAll($sQuery, $this->aDefaultValues);
        $this->sHtml .= "<table class='table Arura-Table'>";
        $this->sHtml .= "<thead><tr>";
        foreach ($this->aDataFile["columns"] as $Column){
            $this->sHtml .= "<th>" . $Column['name'] . "</th>";
        }
        $this->sHtml .= "<th>";
        if (!isset($_GET["action"]) || ($_GET["action"] !== "create" && $_GET["action"] !== "edit")){
            $this->sHtml .= "<a class='btn btn-primary btn-sm' href='".$this->getUrl(["action" => "create"])."'><i class=\"fas fa-plus\"></i></a>";
        }
        $this->sHtml .= "</th></tr></thead><tbody>";
        foreach ($aData as $record){
            $this->sHtml  .= "<tr>";
            foreach ($this->aDataFile["columns"] as $tag => $column){
                switch ($this->aDataFile["columns"][$tag]["type"]){
                    case "dropdown":
                        $this->sHtml  .= "<td>" . $this->aDataFile["columns"][$tag]["options"][$record[$tag]] . "</td>";
                        break;
                    default:
                        $this->sHtml  .= "<td>" . $record[$tag] . "</td>";
                        break;

                }
            }
            $this->sHtml .= "<td>".$this->getActionButtons($record[$this->aDataFile["primarykey"]])."</td>";
            $this->sHtml  .= "</tr>";

        }
        $this->sHtml  .= "</tbody></table>";
    }

    /**
     * @param null $iRowId
     * @return string
     */
    protected function getActionButtons($iRowId = null){
        $s = "<div class='btn-group btn-group-sm'>";
        $s .= "<button class='btn btn-danger btn-delete' href='".$this->getUrl(["action" => "delete", "_key" => $iRowId])."'><i class=\"fas fa-trash\"></i></button>";
        $s .= "<a href='".$this->getUrl(["action" => "edit", "_key" => $iRowId])."' class='btn btn-primary'><i class=\"fas fa-pen\"></i></a>";
        $s .= "</div>";
        return $s;
    }

    /**
     * @param array $aData
     * @param null $sAction
     * @return string
     */
    protected  function buildInputField($aData = [],$sAction = null){
        $sHtml = "<h2>".(($sAction==="save") ? "Toevoegen" : "Bewerken")."</h2>";
        $sHtml .= "<form method='post' action='".$this->getUrl(["action" => $sAction])."' class='form-row bg-secondary p-1 border-4 border-dark rounded m-2'>";
        foreach ($this->aDataFile["columns"] as $tag => $column){
            $value = null;
            if (isset($aData[$tag])){
                $value = $aData[$tag];
            }

            if (!str_contains("auto_increment",$this->aColumnInfo[$tag]["Extra"])){
                $sInputGroup = "<div class='form-group col-xl-3 col-md-3 col-sm-6 col-12'>";
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

                $sInputGroup .= "</div>";
            } else {
                $sInputGroup = "<input type='hidden' name='".$tag."' value='".$value."'/>";
            }

            $sHtml .= $sInputGroup;
        }
        $sHtml .= "<div class='col-md-12'><div class='btn-group'><input type='submit' class='btn btn-success' value='Opslaan'><a class='btn btn-danger' href='".$this->getUrl()."'>Annuleren</a></div></div>";
        $sHtml .= "";
        $sHtml .= "</form>";
        return $sHtml;
    }

    /**
     * @return string
     * @throws Exceptions\Error
     */
    public function __toString()
    {
        return $this->getHTMLTable();
    }

    /**
     * @param string $sFile
     * @param array $aParams
     * @param array $aDefaultValues
     * @param null $sCssId
     * @return Crud
     * @throws Exceptions\Error
     */
    public static function drop($sFile,$aParams = [], $aDefaultValues = [], $sCssId = null){
        return new self($sFile, $aParams, $aDefaultValues, $sCssId);
    }
}