<?php
namespace Arura\Crud;
use Arura\Database;
use Crud\Fields\Field;
use Exception;
use PDO;

class Crud{

    protected $aFields = [];
    protected $aParams = [];
    protected $aDefaultValues = [];
    protected $sCssId = "";
    public $aColumnData = [];
    public $sPrimaryKey = "";
    public $sTable = "";
    protected $pageUrl = "";

    public $oTable;
    public $oForm;

    public $sActions = "";
    protected $sHtml = "";

    protected $isLoaded = false;

    protected $bCanEdit = true;
    protected $bCanInsert = true;
    protected $bCanDelete =true;

    public $db;

    /**
     * Crud constructor.
     * @param string $sTable
     * @param string $sPrimaryKey
     * @param PDO $connection
     */
    public function __construct($sTable, $sPrimaryKey, PDO $connection)
    {
        $this->sTable = $sTable;
        $this->sPrimaryKey = $sPrimaryKey;
        $this->db = new Database();
        $this->oForm = new Form($this);
        $this->oTable = new Table($this);
    }

    /**
     * Get HTML Crud
     * @return string
     * @throws Exceptions\CrudException
     */
    public function __toString()
    {
        $this->load();
        $this->sHtml .= (string)$this->oTable;
        return $this->sHtml;
    }

    /**
     * @throws Exceptions\CrudException
     */
    protected function load($force = false){
        if (!$this->isLoaded || $force){
            $this->CollectColumnData();
            if (isset($_GET["action"])){
                $this->Actions();
            }
            $this->isLoaded = true;
        }
    }

    /**
     * Add GET parameter
     * @param string $key
     * @param string|int $value
     */
    public function addParam($key, $value){
        $this->aParams[$key] = $value;
    }

    /**
     * Add field to CRUD
     * @param Field $oField
     */
    public function addField(Field $oField){
        $this->aFields[] = $oField;
    }

    /**
     * Add default value for all records.
     * Also CRUD shows only records where default values are defined
     * @param string $key
     * @param string $value
     */
    public function addDefaultValue($key,$value){
        $this->aDefaultValues[$key] = $value;
    }

    /**
     * when defined CSS id wil be added to URL so user is wil be redirect to section on page
     * @param string $iD
     */
    public function setCssId($iD){
        $this->sCssId = $iD;
    }

    /**
     * Construct url
     * @param array $aParams
     * @return string
     */
    public function getUrl($aParams = []){
        $aParas = array_merge($aParams, $this->aParams);
        return (empty($this->getPageUrl())) ? $_SERVER["REDIRECT_URL"] : $this->getPageUrl() . (!empty($aParas) ? "?".http_build_query($aParas) : null) . (!empty($this->sCssId) ? "#" . $this->sCssId : null);
    }

    /**
     * Get all fields
     * @return array
     */
    public function getFields(): array
    {
        return $this->aFields;
    }

    /**
     * @return array
     */
    public function getDefaultValues(): array
    {
        return $this->aDefaultValues;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->aParams;
    }

    /**
     * @throws Exceptions\CrudException
     */
    protected function CollectColumnData(){
        foreach ($this->db->fetchAll("SHOW columns FROM {$this->sTable}") as $column){
            $this -> aColumnData[$column["Field"]] = $column;
        }
    }


    private function Actions(){
        $this->sActions = $_GET['action'];
        if (isset($_POST["_save"])){
            unset($_POST["_save"]);
        }
        if (isset($_POST["_token_"])){
            unset($_POST["_token_"]);
        }
        try{
            switch ($this->sActions){
                case 'create':
                    if ($this->canInsert()){
                        //Add create form to HTML
                        $this -> sHtml .= (string)$this->oForm . $this->sHtml;
                    }
                    break;
                case 'save':
                    if ($this->canInsert()){
                        //Create record
                        foreach ($_POST as $sName => $sValue){
                            if (self::str_contains("auto_increment",$this->aColumnData[$sName]['Extra'])){
                                unset($_POST[$sName]);
                            }
                        }
                        $this->db->createRecord($this->sTable, array_merge($this->aDefaultValues, $_POST));
                        header('Location:' . $this->getUrl());
                    }
                    break;
                case 'edit':
                    if ($this->canEdit()){
                        //If the primary key of the record is defined add Edit from to HTML
                        if (isset($_GET['_key'])){
                            $this -> sHtml .= $this->oForm->getForm(). $this->sHtml;
                        }
                    }
                    break;
                case 'update':
                    if ($this->canEdit()){
                        $this->db->updateRecord($this->sTable, array_merge($this->aDefaultValues, $_POST), $this->sPrimaryKey);
                        if ($this->db->isQuerySuccessful()){
                            header('Location:' . $this->getUrl());
                        }
                    }
                    break;
                case 'delete':
                    if ($this->canDelete()){
                        $this->db->query('DELETE FROM '. $this->sTable . ' WHERE ' . $this->sPrimaryKey . ' = :'.$this->sPrimaryKey, [$this->sPrimaryKey => $_GET['_key']]);
                        if ($this->db->isQuerySuccessful()){
                            header('Location:' . $this->getUrl());
                        }
                    }
                    break;
            }
        } catch (Exception $e){
            header('Location:' . $this->getUrl());
        }
    }

    /**
     * @return bool
     */
    public function canEdit(): bool
    {
        return $this->bCanEdit;
    }

    /**
     * @param bool $bCanEdit
     */
    public function setCanEdit(bool $bCanEdit)
    {
        $this->bCanEdit = $bCanEdit;
    }

    /**
     * @return bool
     */
    public function canInsert(): bool
    {
        return $this->bCanInsert;
    }

    /**
     * @param bool $bCanInsert
     */
    public function setCanInsert(bool $bCanInsert)
    {
        $this->bCanInsert = $bCanInsert;
    }

    /**
     * @return bool
     */
    public function canDelete(): bool
    {
        return $this->bCanDelete;
    }

    /**
     * @param bool $bCanDelete
     */
    public function setCanDelete(bool $bCanDelete)
    {
        $this->bCanDelete = $bCanDelete;
    }

    public static function str_contains($needle, $haystack)
    {
        return strpos($haystack, $needle) !== false;
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        $this->load();
        return $this->oTable;
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        $this->load();
        return $this->oForm;
    }

    /**
     * @return string
     */
    public function getPageUrl(): string
    {
        return $this->pageUrl;
    }

    /**
     * @param string $pageUrl
     */
    public function setPageUrl(string $pageUrl)
    {
        $this->pageUrl = $pageUrl;
    }


}
