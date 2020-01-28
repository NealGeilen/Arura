<?php
namespace Arura\Crud;

use Arura\Exceptions\Error;

class Table {

    protected $oCrud;
    protected $aRecords = [];


    /**
     * Table constructor.
     * @param Crud $oCrud
     */
    public function __construct(Crud $oCrud)
    {
        $this->oCrud = $oCrud;
    }

    /**
     * Get HTML Table
     * @return string
     * @throws Error
     */
    public function __toString()
    {
        $this->collectRecords();
        return $this->buildTable();
    }

    /**
     * Collect all records from table.
     * When default values are defined only record will be collect where default values are defined
     * @throws Error
     */
    public function collectRecords(){
        //Load all records and set in $aRecords
        $sQuery = "SELECT * FROM ". $this->oCrud->sTable;
        if (!empty($this->oCrud->getDefaultValues())){
            $sQuery .= " WHERE ";
            foreach ($this->oCrud->getDefaultValues() as $key => $value){
                $sQuery .= $key . ' = :' . $key. ', ';
            }
            $sQuery = trim($sQuery,", ");
        }
        //Execute query
        $this->aRecords =  $this->oCrud->db->fetchAll($sQuery, $this->oCrud->getDefaultValues());
    }

    /**
     * get HTML buttons for every record
     * @param int $iRowId
     * @return string
     */
    protected function getActionButtons($iRowId = null){
        $s = "<div class='btn-group btn-group-sm'>";
        $s .= "<a class='btn btn-danger' href='{$this->oCrud->getUrl(["action" => Actions::DELETE, "_key" => $iRowId])}'><i class=\"fas fa-trash\"></i></a>";
        $s .= "<a class='btn btn-primary' href='{$this->oCrud->getUrl(["action" => Actions::EDIT, "_key" => $iRowId])}'><i class=\"fas fa-pen\"></i></a>";
        $s .= "</div>";
        return $s;
    }

    /**
     * Build HTML table and add records to table
     */
    private function buildTable(){
        $sHtml = "";
        //Start HTML table
        $sHtml .= "<table class='table table-striped table-light Arura-Table'>";
        $sHtml .= "<thead class='thead-light'><tr>";
        foreach ($this->oCrud->getFields() as $oField){
            $sHtml .= "<th>{$oField->getName()}</th>";
        }
        $sHtml .= "<th>";
        //If action is not create or edit add btn for creation
        if (!isset($_GET["action"]) || ($_GET["action"] !== Actions::CREATE && $_GET["action"] !== Actions::EDIT)){
            $sHtml .= "<a class='btn btn-primary btn-sm' href='{$this->oCrud->getUrl(["action" => "create"])}'><i class=\"fas fa-plus\"></i></a>";
        }
        $sHtml .= "</th></tr></thead><tbody>";
        //Create rows
        foreach ($this->aRecords as $record){
            $sHtml  .= "<tr>";
            //add columns
            foreach ($this->oCrud->getFields() as $oField){
                $sHtml .= "<td>{$oField->getData($record[$oField->getTag()])}</td>";
            }
            $sHtml .= "<td>{$this->getActionButtons($record[$this->oCrud->sPrimaryKey])}</td>";
            $sHtml  .= "</tr>";

        }
        $sHtml  .= "</tbody></table>";
        return $sHtml;
    }


}