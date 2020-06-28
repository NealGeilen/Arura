<?php
namespace Arura;

/**
 * Class DataBaseSync
 * @package Arura
 */
class DataBaseSync extends Modal {

    /**
     * @var string
     */
    private $sDataFilesDir;

    /**
     * @var array
     */
    private $aTables = [];


    /**
     * DataBaseSync constructor.
     * @param string $sDataFilesDir
     */
    public function __construct($sDataFilesDir = "")
    {
        parent::__construct();
        $this->sDataFilesDir = $sDataFilesDir;
        $this->loadDataFiles();
    }

    /**
     *
     */
    private function loadDataFiles(){
        if (is_dir($this->sDataFilesDir)){
            foreach (scandir($this->sDataFilesDir) as $sFile){
                $sPath = $this->sDataFilesDir . DIRECTORY_SEPARATOR . $sFile;
                if (pathinfo($sPath, PATHINFO_EXTENSION) === "json"){
                    $this->aTables[pathinfo($sPath, PATHINFO_FILENAME)] = json_array_decode(file_get_contents($sPath));
                }
            }
        }
    }

    public function getChanges(){
        $Changes = [];
        foreach ($this->aTables as $sTable => $aData){
            if (count($this -> db -> fetchall("SHOW TABLES LIKE '" .$sTable."'")) > 0){
                //Table exits
                foreach ($aData["columns"] as $sColumn => $aSQL){
                    $aColumn = $this->getColumn($sColumn, $sTable);
                    if (!empty($aColumn)){
                        $b["Field"] = $sColumn;
                        $b = array_merge($b, $this->aTables[$sTable]["columns"][$sColumn]);
                        if ($aColumn !== $b){
                            //Column needs changing;
                            $Changes[] = "Kolom: '{$sColumn}' van tabel: '{$sTable}' updaten";
                        }
                    } else {
                        $Changes[] = "Kolom: '{$sColumn}' van tabel: '{$sTable}' aanmaken";
                    }
                }
                //Check for columns in databse what are not needed. when found drop these columns
                foreach ($this->getAllColumns($sTable) as $column){
                    if (!isset($this->aTables[$sTable]["columns"][$column["Field"]])){
                        $Changes[] = "Kolom: '{$column["Field"]}' van tabel: '{$sTable}' moet verwijdert worden";
                    }
                }
            } else {
                //Create Table
                $Changes[] = "Nieuwe tabel : {$sTable}";
            }
        }
        //Drop Tables if they dont have a file, When found drop table
        foreach ($this->getAllTables() as $sTable){
            if (!isset($this->aTables[$sTable]) && substr( $sTable, 0, 3 ) !== "SA_" ){
                $Changes[] = "tabel: '{$sTable}' moet verwijderd worden";
            }
        }
        return $Changes;
    }

    /**
     * @throws Exceptions\Error
     */
    public function Reload(){
        foreach ($this->aTables as $sTable => $aData){
            if (count($this -> db -> fetchall("SHOW TABLES LIKE '" .$sTable."'")) > 0){
                //Table exits
                foreach ($aData["columns"] as $sColumn => $aSQL){
                    $aColumn = $this->getColumn($sColumn, $sTable);
                    if (!empty($aColumn)){
                        $b["Field"] = $sColumn;
                        $b = array_merge($b, $this->aTables[$sTable]["columns"][$sColumn]);
                        if ($aColumn !== $b){
                            //Column needs changing;
                            $this->alterColumn($sTable,$sColumn,$this->aTables[$sTable]["columns"][$sColumn]);
                        }
                    } else {
                        $this->addColumn($sTable, $sColumn,$aSQL);
                    }
                }
                //Check for columns in databse what are not needed. when found drop these columns
                foreach ($this->getAllColumns($sTable) as $column){
                    if (!isset($this->aTables[$sTable]["columns"][$column["Field"]])){
                        $this->dropColumn($sTable, $column["Field"]);
                    }
                }
                $this->fillData($sTable);
            } else {
                //Create Table
                $this->createTable($sTable);
            }
        }
        //Drop Tables if they dont have a file, When found drop table
        foreach ($this->getAllTables() as $sTable){
            if (!isset($this->aTables[$sTable]) && substr( $sTable, 0, 3 ) !== "SA_" ){
                $this->dropTable($sTable);
            }
        }
    }

    /**
     * @param string $sTableName
     * @throws Exceptions\Error
     */
    private function fillData($sTableName = ""){
        if (isset($this->aTables[$sTableName]["data"])){
            foreach ($this->aTables[$sTableName]["data"] as $aData){
                if (!$this->doesRecordExits($sTableName, $aData)){
                    $this->db->createRecord($sTableName, $aData);
                }
            }
        }
    }

    /**
     * @param string $sTableName
     * @throws Exceptions\Error
     */
    private function createTable($sTableName = ""){
        $aTable = $this->aTables[$sTableName];
        $sQuery = "CREATE TABLE " . $sTableName . " (";
        foreach ($aTable["columns"] as $sColumnName => $aColumnData){
            $sQuery .= $sColumnName . " " . $this->ColDataToStr($aColumnData) . ", ";
        }
        $sQuery = trim($sQuery,", ");
        $sQuery .= ")";
        $this->db->query($sQuery);
        $this->fillData($sTableName);
    }

    /**
     * @param string $sTableName
     * @param array $aRecord
     * @return bool
     * @throws Exceptions\Error
     */
    private function doesRecordExits($sTableName = "", $aRecord = []){
        $sQuery = "SELECT * FROM " .$sTableName . " WHERE ";
        foreach ($aRecord as $key => $value){
            $sQuery.= $key . " = '" . $value . "' AND ";
        }
        $sQuery = trim($sQuery,"AND ");
        return (count($this->db->fetchAll($sQuery)) !== 0 );
    }

    /**
     * @param string $sColumn
     * @param string $sTable
     * @return mixed
     * @throws Exceptions\Error
     */
    private function getColumn($sColumn = "", $sTable = ""){
        return $this->db->fetchRow("SHOW COLUMNS FROM `".$sTable."` LIKE '".$sColumn."'");
    }

    /**
     * @param string $sTable
     * @return array
     * @throws Exceptions\Error
     */
    private function getAllColumns($sTable = ""){
        return $this->db->fetchAll("SHOW COLUMNS FROM `".$sTable."`");
    }

    /**
     * @return array
     * @throws Exceptions\Error
     */
    private function getAllTables(){
        return $this -> db -> fetchAllColumn("SHOW TABLES");
    }


    /**
     * @param string $sTable
     * @param string $sName
     * @param array $aData
     * @throws Exceptions\Error
     */
    private function addColumn($sTable = "", $sName = "", $aData = []){
        $sSQL = "ALTER TABLE " . $sTable . " ADD " . $sName . " " . $this->ColDataToStr($aData);
        $this->db->query($sSQL);
    }

    /**
     * @param string $sTable
     * @param string $sName
     * @param array $aData
     * @throws Exceptions\Error
     */
    private function alterColumn($sTable = "", $sName = "", $aData = []){
        $sSQL = "ALTER TABLE " . $sTable . " MODIFY COLUMN " . $sName . " " . $this->ColDataToStr($aData);
        $this->db->query($sSQL);
    }

    /**
     * @param string $sTable
     * @param string $sColumn
     * @throws Exceptions\Error
     */
    private function dropColumn($sTable = "", $sColumn = ""){
        $this->db->query("ALTER TABLE ".$sTable." DROP COLUMN " . $sColumn);
    }

    /**
     * @param string $sTable
     * @throws Exceptions\Error
     */
    private function dropTable($sTable = ""){
        $this->db->query("DROP TABLE ".$sTable);
    }

    /**
     * @param array $aData
     * @return string
     */
    private function ColDataToStr($aData = []){
        $s ="";
        $s .= $aData["Type"] . " ";
        $s .= (($aData["Null"] === "NO") ? "NOT NULL " : null) ;
        $s .= (($aData["Key"] === "PRI") ? "PRIMARY KEY " : null);
        $s .= (!empty($aData["Default"]))? "DEFAULT '" .$aData["Default"]."' " : null;
        $s .= $aData["Extra"];
        return $s;

    }

    /**
     * @param string $sTable
     * @param bool $andData
     * @return array
     * @throws Exceptions\Error
     */
    public function getJsonFormat($sTable = "", $andData = false){
        $aColumns = $this->db->fetchAll("SHOW COLUMNS FROM `".$sTable."`");
        $aList = [];
        foreach ($aColumns as $aColumn){
            $sName = $aColumn["Field"];
            unset($aColumn["Field"]);
            $aList["columns"][$sName]  = $aColumn;
        }
        if ($andData){
            $aList["data"] = $this->db->fetchAll("SELECT * FROM " . $sTable);
        }
        return $aList;
    }


}