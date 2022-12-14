<?php
namespace Arura\SecureAdmin;
use Error;
use PDOStatement;
use Symfony\Component\VarDumper\VarDumper;

class Database extends \Arura\Database {

    protected $sKey;
    protected $sPrimaryKey;

    /**
     * Database constructor.
     * @param string $sKey
     * @param string $sPrimaryKey
     * @param null $host
     * @param null $username
     * @param null $password
     * @param null $database
     */
    public function __construct($sKey = "", $sPrimaryKey = "", $host = null, $username = null, $password = null, $database = null)
    {
        $this->sKey = $sKey;
        $this->sPrimaryKey = $sPrimaryKey;
        parent::__construct($host, $username, $password, $database);
    }

    /**
     * @param string $sTable
     * @param array $aData
     * @return int
     * @throws \Arura\Exceptions\Error
     */
    public function createRecord($sTable = "", $aData = [])
    {
        $record = [];
        foreach ($aData as $sField => $sValue){
            if ($sField === $this->sPrimaryKey){
                $record[$sField] = $sValue;
            } else {
                $record[$sField] = $this->encrypt($sValue);
            }
        }
        return parent::createRecord($sTable, $record);
    }

    /**
     * @param string $sTable
     * @param array $aData
     * @param string $sPrimaryKey
     * @return PDOStatement
     * @throws \Arura\Exceptions\Error
     */
    public function updateRecord($sTable = "", $aData = [], $sPrimaryKey = "")
    {
        $record = [];
        foreach ($aData as $sField => $sValue){
            if ($sField === $sPrimaryKey){
                $record[$sField] = $sValue;
            } else {
                $record[$sField] = $this->encrypt($sValue);
            }
        }
        return parent::updateRecord($sTable, $record, $sPrimaryKey);
    }

    /**
     * @param $sTable
     * @param $sPrimaryId
     * @return array
     * @throws \Arura\Exceptions\Error
     */
    public function SelectAll($sTable, $sPrimaryId) : array
    {
        $aData = parent::fetchAll("SELECT * FROM " . $sTable);
        $aList = [];
        foreach ($aData as $aRecord){
            $record = [];
            foreach ($aRecord as $sColumn => $sValue){
                if ($sColumn === $sPrimaryId){
                    $record[$sColumn] = $sValue;
                } else {
                    $record[$sColumn] = $this->decrypt($sValue);
                }
            }
            $aList[] = $record;
        }
        return $aList;
    }

    /**
     * @param $sTable
     * @param $sValue
     * @param $sPrimaryKey
     * @return array
     * @throws \Arura\Exceptions\Error
     */
    public function SelectRow($sTable, $sValue, $sPrimaryKey)
    {
        $aRecord = parent::fetchRow("SELECT * FROM " . $sTable. " WHERE " . $sPrimaryKey . " = :" . $sPrimaryKey, [$sPrimaryKey => $sValue]);
        $aList = [];
        foreach ($aRecord as $sColumn => $sValue){
            if ($sColumn === $sPrimaryKey){
                $aList[$sColumn] = $sValue;
            } else {
                $aList[$sColumn] = $this->decrypt($sValue);
            }

        }
        return $aList;
    }

    /**
     * @param string $sData
     * @return false|string
     */
    protected function decrypt($sData = ""){
        if ($sData === null || $sData === ""){
            return null;
        }
        $c = base64_decode($sData);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $this->sKey, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->sKey, $as_binary=true);
        if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
        {
            return $original_plaintext;
        } else {
            throw new Error();
        }
    }

    /**
     * @param string $sData
     * @return string
     */
    protected function encrypt($sData = ""){
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($sData, $cipher, $this->sKey, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $this->sKey, $as_binary=true);
        return base64_encode( $iv.$hmac.$ciphertext_raw );
    }

}
