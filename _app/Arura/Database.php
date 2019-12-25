<?php
namespace Arura;

use Arura\Exceptions\Error;

class Database{

    protected static $connection;

    private $host = '';

    private $username = '';

    private $password = '';

    private $database = '';

    private $queryState = false;

    /**
     * Database constructor.
     * @param null $host
     * @param null $username
     * @param null $password
     * @param null $database
     */
    public function __construct(
        $host = null,
        $username = null,
        $password = null,
        $database = null
    )
    {
        if (is_null($host)) {
            $host = __DB_HOST__;
        }

        $this->host = $host;

        if (is_null($username)) {
            $username = __DB_USERNAME__;
        }

        $this->username = $username;

        if (is_null($password)) {
            $password = __DB_PASSWORD__;
        }

        $this->password = $password;

        if (is_null($database)) {
            $database = __DB_NAME__;
        }

        $this->database = $database;


    }
    public static function ExecQuery($statment, array $parameters = []){
        $db = new self();
        return $db -> query($statment, $parameters);
    }

    /**
     * Connect to database
     */
    protected function connect(){
        if (!isset(self::$connection)){
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->database;
            self::$connection = new \PDO($dsn, $this->username,$this->password);
        }
    }

    /**
     * PDO secure query
     * @param {String} $statment
     * @param {Array} $parameters
     * @return mixed
     */
    public function query($statment, array $parameters = []){
        $this->queryState = false;
        $this->connect();
        $stmt = self::$connection->prepare($statment);
        $this -> queryState = $stmt->execute($parameters);
        if (!$this ->queryState){
            $sError = ((string)$stmt->errorInfo()) . " " . $statment;
            throw new Error($sError);
        }
        return $stmt;
    }

    /**
     * Query function and return multiple rows assoc
     * @param {String} $statment
     * @param {Array} $parameters
     * @return {Array}
     */
    public function fetchAll($statment, array $parameters = []){
        return $this->query($statment,$parameters)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Query function and return multiple rows assoc
     * @param {String} $statment
     * @param {Array} $parameters
     * @return {Array}
     */
    public function fetchAllColumn($statment, array $parameters = []){
        return $this->query($statment,$parameters)->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Query function and return singel row assoc
     * @param {String} $statment
     * @param {Array} $parameters
     * @return {Array}
     */
    public function fetchRow($statment, array $parameters = []){
        return $this->query($statment,$parameters)->fetch(\PDO::FETCH_ASSOC);
    }

    function getLastInsertId(){
        return (int)self::$connection -> lastInsertId();
    }

    public function isQuerySuccessful(){
        return $this -> queryState;
    }

    public function createRecord($sTable,$aData){
        $sQuery = 'INSERT INTO '.$sTable.' SET ';
        $this->buildSetString($sQuery,$aData);
        return $this -> getLastInsertId();
    }

    public function updateRecord($sTable, $aData, $sPrimaryKey){
        $sQuery = 'UPDATE '.$sTable.' SET ';
        return $this->buildSetString($sQuery,$aData,$sPrimaryKey);
    }

    private function buildSetString($sQuery, $aData = [], $sPrimaryKey = null){
        foreach ($aData as $sKey => $sValue){
            if ($sKey !== $sPrimaryKey){
                $sQuery .= $sKey . ' = :' . $sKey. ', ';
            }
        }
        $sQuery = trim($sQuery,", ");
        if (!is_null($sPrimaryKey)){
            $sQuery .= ' WHERE ' .$sPrimaryKey . ' = :' .$sPrimaryKey;
        }
        return $this->query($sQuery,$aData);
    }



}