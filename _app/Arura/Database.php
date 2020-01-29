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

    /**
     * @param string $statment
     * @param array $parameters
     * @return bool|\PDOStatement
     * @throws Error
     */
    public static function ExecQuery($statment = "", array $parameters = []){
        $db = new self();
        return $db -> query($statment, $parameters);
    }

    /**
     * @return \PDO
     */
    protected function connect() :\PDO
    {
        if (!isset(self::$connection)){
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->database;
            self::$connection = new \PDO($dsn, $this->username,$this->password);
        }
        return  self::$connection;
    }


    /**
     * @param string $statment
     * @param array $parameters
     * @return \PDOStatement
     * @throws Error
     */
    public function query($statment = "", array $parameters = []){
        $this->queryState = false;
        $stmt = $this->connect()->prepare($statment);
        $this -> queryState = $stmt->execute($parameters);
        $GLOBALS["Querys"][] = ["query"=>$statment,"source"=>debug_backtrace()];
        if (!$this ->queryState){
            $sError = (json_encode($stmt->errorInfo())) . " " . $statment;
            throw new Error($sError);
        }
        return $stmt;
    }

    /**
     * @param string $statment
     * @param array $parameters
     * @return array
     * @throws Error
     */
    public function fetchAll($statment = "", array $parameters = []){
        return $this->query($statment,$parameters)->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * @param string $statment
     * @param array $parameters
     * @return array
     * @throws Error
     */
    public function fetchAllColumn($statment = "", array $parameters = []){
        return $this->query($statment,$parameters)->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @param string $statment
     * @param array $parameters
     * @return mixed
     * @throws Error
     */
    public function fetchRow($statment = "", array $parameters = []){
        return $this->query($statment,$parameters)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @return int
     */
    function getLastInsertId(){
        return (int)$this->connect() -> lastInsertId();
    }

    /**
     * @return bool
     */
    public function isQuerySuccessful(){
        return $this -> queryState;
    }

    /**
     * @param string $sTable
     * @param array $aData
     * @return int
     * @throws Error
     */
    public function createRecord($sTable = "", $aData = []){
        $sQuery = 'INSERT INTO '.$sTable.' SET ';
        $this->buildSetString($sQuery,$aData);
        return $this -> getLastInsertId();
    }

    /**
     * @param string $sTable
     * @param array $aData
     * @param string $sPrimaryKey
     * @return \PDOStatement
     * @throws Error
     */
    public function updateRecord($sTable = "", $aData = [], $sPrimaryKey = ""){
        $sQuery = 'UPDATE '.$sTable.' SET ';
        return $this->buildSetString($sQuery,$aData,$sPrimaryKey);
    }

    /**
     * @param string $sQuery
     * @param array $aData
     * @param string $sPrimaryKey
     * @return \PDOStatement
     * @throws Error
     */
    private function buildSetString($sQuery = "", $aData = [], $sPrimaryKey = ""){
        foreach ($aData as $sKey => $sValue){
            if ($sKey !== $sPrimaryKey){
                $sQuery .= $sKey . ' = :' . $sKey. ', ';
            }
        }
        $sQuery = trim($sQuery,", ");
        if (!empty($sPrimaryKey)){
            $sQuery .= ' WHERE ' .$sPrimaryKey . ' = :' .$sPrimaryKey;
        }
        return $this->query($sQuery,$aData);
    }



}