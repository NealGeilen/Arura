<?php
function str_contains($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
}

function str_random($length = 15, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

function error_reporter($errno, $errstr, $errfile, $errline){
    // check if error is suppressed
    if (error_reporting() == 0) {
        return;
    }
    // check if severity matches error_reporting level
    if (error_reporting() & $errno) {
        throw new \ErrorException($errstr, 500, $errno, $errfile, $errline);
    }
}

function json_array_decode($sString){
    if (isJson($sString)){
        return json_decode($sString,true);
    }
}

function isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

/**
 * Return string before needle if it exists.
 *
 * @param string $str
 * @param mixed $needle
 * @return string
 */
function str_before($str, $needle)
{
    $pos = strpos($str, $needle);

    return ($pos !== false) ? substr($str, 0, $pos) : $str;
}

/**
 * Return string after needle if it exists.
 *
 * @param string $str
 * @param mixed $needle
 * @return string
 */
function str_after($str, $needle)
{
    $pos = strpos($str, $needle) + 1;

    return ($pos !== false) ? substr($str, $pos, strlen($str)) : $str;
}




function getHash($sTable, $sColumn, $iLength = 25){
    $db = new \Arura\Database();
    do {
        $str = str_random($iLength);
        $aData = $db -> fetchRow("SELECT " . $sColumn . " FROM " . $sTable . " WHERE " .$sColumn . " = :".$sColumn, [$sColumn => $str]);
    } while(isset($aData[$sColumn]));
    return $str;
}


function str_dir_strip($str){
    $str = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $str);
    $str = mb_ereg_replace("([\.]{2,})", '', $str);
    return $str;
}