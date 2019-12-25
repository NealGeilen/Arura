<?php
namespace Arura;
use Arura\Database;
use Arura\User\User;

class Crud extends Database {

    protected $sHtml = "";
    protected $aDataFile;
    protected $aColumnInfo;
    protected $aParas;
    protected $aDefaultValues;
    protected $sCssId;

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

    protected function getUrl($aParams = []){
        return $_SERVER["REDIRECT_URL"] ."?". http_build_query(array_merge($aParams, $this->aParas)) . "#" . $this->sCssId;
    }

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
        } catch (\Exception $e){
            header('Location:' . $this->getUrl());
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
        $aData = $this->fetchAll("SELECT * FROM ". $this->aDataFile["table"]);
        $this->sHtml .= "<table class='table'>";
        $this->sHtml .= "<thead><tr>";
        foreach ($this->aDataFile["columns"] as $Column){
            $this->sHtml .= "<th>" . $Column['name'] . "</th>";
        }
        $this->sHtml .= "<th>";
        if ($_GET["action"] !== "create" && $_GET["action"] !== "edit"){
            $this->sHtml .= "<a class='btn btn-primary' href='".$this->getUrl(["action" => "create"])."'><i class=\"fas fa-plus\"></i></a>";
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
        if (count($aData) === 0){
            $this->sHtml .= "<td colspan='".count($this->aDataFile["columns"])."'><div class='alert alert-info'>Er zijn geen gegegevens aanwezig</div></td>";
        }
        $this->sHtml  .= "</tbody></table>";
    }

    protected function getActionButtons($iRowId = null){
        $s = "<div class='btn-group'>";
        $s .= "<a href='".$this->getUrl(["action" => "delete", "_key" => $iRowId])."' class='btn btn-danger'><i class=\"fas fa-trash\"></i></a>";
        $s .= "<a href='".$this->getUrl(["action" => "edit", "_key" => $iRowId])."' class='btn btn-primary'><i class=\"fas fa-pen\"></i></a>";
        $s .= "</div>";
        return $s;
    }

    protected  function buildInputField($aData = [],$sAction = null){
        $sHtml = "<h2>".(($sAction==="save") ? "Toevoegen" : "Bewerken")."</h2>";
        $sHtml .= "<form method='post' action='".$this->getUrl(["action" => $sAction])."' class='form-row bg-secondary p-1 border-4 border-dark rounded'>";
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

    public function __toString()
    {
        return $this->getHTMLTable();
    }

    public static function drop($sFile,$aParams = [], $aDefaultValues = [], $sCssId = null){
        return new self($sFile, $aParams, $aDefaultValues, $sCssId);
    }
}