<?php
namespace Arura\Crud;

class Table {

    /**
     * Crud object
     * @var Crud
     */
    protected $oCrud;
    /**
     * Records for table
     * @var array
     */
    protected $aRecords = [];


    /**
     * Css classes list for table
     * @var array
     */
    protected $aCssTableClasses = ["table", "table-striped", "table-hover", "Arura-Table"];
    /**
     * Css classes list for table thead
     * @var array
     */
    protected $aCssTheadClasses = [];

    /**
     * Visable name of button for edit;
     * @var string
     */
    protected $sEditBtn = "Aanpassen";
    /**
     * Visable name of button for insert;
     * @var string
     */
    protected $sInsterBtn = "Toevoegen";

    /**
     * Visable name of button for delet;
     * @var string
     */
    protected $sDeleteBtn = "Verwijderen";


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
     * @throws Exceptions\CrudException
     */
    public function __toString()
    {
        $this->collectRecords();
        return $this->buildTable();
    }

    /**
     * Collect all records from table.
     * When default values are defined only record will be collect where default values are defined
     * @throws Exceptions\CrudException
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
        if ($this->oCrud->canEdit()){
            $s .= "<a class='btn btn-primary' href='{$this->oCrud->getUrl(["action" => Actions::EDIT, "_key" => $iRowId])}'>{$this->sEditBtn}</a>";
        }
        if ($this->oCrud->canDelete()){
            $s .= "<a class='btn btn-danger' href='{$this->oCrud->getUrl(["action" => Actions::DELETE, "_key" => $iRowId])}'>{$this->sDeleteBtn}</a>";
        }
        $s .= "</div>";
        return $s;
    }

    /**
     * Build HTML table and add records to table
     */
    private function buildTable(){
        $sHtml = "";
        //Start HTML table
        $sHtml .= "<table class='{$this->getCssTableClasses()}'>";
        $sHtml .= "<thead class='{$this->getCssTheadClasses()}'><tr>";
        foreach ($this->oCrud->getFields() as $oField){
            $sHtml .= "<th>{$oField->getName()}</th>";
        }
        if ($this->oCrud->canEdit() || $this->oCrud->canDelete() || $this->oCrud->canInsert()){
            $sHtml .= "<th>";
            //If action is not create or edit add btn for creation
            if (!isset($this->oCrud->sActions) || ($this->oCrud->sActions !== Actions::CREATE && $this->oCrud->sActions !== Actions::EDIT) && $this->oCrud->canInsert()){
                $sHtml .= "<a class='btn btn-primary btn-sm' href='{$this->oCrud->getUrl(["action" => "create"])}'>{$this->sInsterBtn}</a>";
            }
            $sHtml .= "</th>";
        }
        $sHtml.="</tr></thead><tbody>";
        //Create rows
        foreach ($this->aRecords as $record){
            $sHtml  .= "<tr>";
            //add columns
            foreach ($this->oCrud->getFields() as $oField){
                $sHtml .= "<td>{$oField->getData($record[$oField->getTag()])}</td>";
            }
            if ($this->oCrud->canDelete() || $this->oCrud->canEdit()){
                $sHtml .= "<td>{$this->getActionButtons($record[$this->oCrud->sPrimaryKey])}</td>";
            }
            $sHtml  .= "</tr>";

        }
        $sHtml  .= "</tbody></table>";
        return $sHtml;
    }

    /**
     * @return string
     */
    public function getCssTableClasses(): string
    {
        return join(" ", $this->aCssTableClasses);
    }

    /**
     * @param array $aCssTableClasses
     */
    public function setCssTableClasses(array $aCssTableClasses)
    {
        $this->aCssTableClasses = $aCssTableClasses;
    }

    /**
     * @param string $aCssTableClasses
     */
    public function addCssTableClasses(string $aCssTableClasses)
    {
        $this->aCssTableClasses[] = $aCssTableClasses;
    }

    /**
     * @return string
     */
    public function getCssTheadClasses(): string
    {
        return join(" ", $this->aCssTheadClasses);
    }

    /**
     * @param array $aCssTheadClasses
     */
    public function setCssTheadClasses(array $aCssTheadClasses)
    {
        $this->aCssTheadClasses = $aCssTheadClasses;
    }

    /**
     * @param string $sCssTheadClasses
     */
    public function addCssTheadClasses(string $sCssTheadClasses)
    {
        $this->aCssTheadClasses[] = $sCssTheadClasses;
    }

    /**
     * @param string $sDeleteBtn
     */
    public function setDeleteBtn(string $sDeleteBtn)
    {
        $this->sDeleteBtn = $sDeleteBtn;
    }

    /**
     * @param string $sInsterBtn
     */
    public function setInsterBtn(string $sInsterBtn)
    {
        $this->sInsterBtn = $sInsterBtn;
    }

    /**
     * @param string $sEditBtn
     */
    public function setEditBtn(string $sEditBtn)
    {
        $this->sEditBtn = $sEditBtn;
    }


}