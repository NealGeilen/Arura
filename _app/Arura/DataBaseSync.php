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
            var_dump("SHOW TABLES LIKE " .$sTable);
            if (count($this -> db -> fetchall("SHOW TABLES LIKE " .$sTable))> 0){
                var_dump($sTable);
            }


        }


    }


}