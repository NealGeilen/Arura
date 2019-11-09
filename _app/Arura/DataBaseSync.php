<?php
namespace Arura;

class DataBaseSync{

    private $db;

    private $sDataFilesDir;

    private $aTables = [];


    public function __construct($sDataFilesDir)
    {
        $this->db = new \NG\Database();
        $this->sDataFilesDir = $sDataFilesDir;
        $this->loadDataFiles();
    }

    private function loadDataFiles(){
        if (is_dir($this->sDataFilesDir)){
            foreach (scandir($this->sDataFilesDir) as $sFile){
                $sPath = $this->sDataFilesDir . DIRECTORY_SEPARATOR . $sFile;
                if (pathinfo($sPath, PATHINFO_EXTENSION) === "json"){
                    var_dump(basename($sPath));
                    $this->aTables[pathinfo($sPath, PATHINFO_FILENAME)] = json_array_decode(file_get_contents($sPath));
                }
            }
        }
    }

    public function Reload(){
        foreach ($this->aTables as $sTable => $aData){
            if ($this -> db -> fetchRow("SHOW TABLES LIKE '" .$sTable."'")){
                $this->fillData($sTable);
            } else {
                $this->createTable($sTable);
            }
        }


    }

    private function fillData($sTableName){
        foreach ($this->aTables[$sTableName]["data"] as $aData){
            if (!$this->doesRecordExits($sTableName, $aData)){
                $this->db->createRecord($sTableName, $aData);
            }
        }
    }

    private function createTable($sTableName){
        $aTable = $this->aTables[$sTableName];
        $sQuery = "CREATE TABLE " . $sTableName . " (";
        foreach ($aTable["columns"] as $sColumnName => $sColumnsData){
            $sQuery .= $this->formatColumn($sColumnName,$sColumnsData) . ", ";
        }
        $sQuery = trim($sQuery,", ");
        $sQuery .= ")";
        $this->db->query($sQuery);
        $this->fillData($sTableName);
    }

    private function formatColumn($sColumnName,$sColumnsData){
        return $sColumnName ." ". $sColumnsData;
    }

    private function doesRecordExits($sTableName, $aRecord){
        $sQuery = "SELECT * FROM " .$sTableName . " WHERE ";
        foreach ($aRecord as $key => $value){
            $sQuery.= $key . " = '" . $value . "' AND ";
        }
        $sQuery = trim($sQuery,"AND ");
        return (count($this->db->fetchAll($sQuery)) !== 0 );
    }


}