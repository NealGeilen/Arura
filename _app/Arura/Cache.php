<?php
namespace Arura;

class Cache{

    public static $Types = array(
        'otf' => "font/otf",
        'css'     => 'text/css',
    'csv'     => 'text/csv',
    'gif'     => 'image/gif',
    'htm'     => 'text/html',
    'html'    => 'text/html',
    'ico'     => 'image/x-icon',
    'ics'     => 'text/calendar',
    'ief'     => 'image/ief',
    'ifb'     => 'text/calendar',
    'jpe'     => 'image/jpeg',
    'jpeg'    => 'image/jpeg',
    'jpg'     => 'image/jpeg',
    'js'      => 'application/x-javascript',
    'json'    => 'application/json',
    'pdf'     => 'application/pdf',
    'png'     => 'image/png',
    'tif'     => 'image/tiff',
    'tiff'    => 'image/tiff',
    'txt'     => 'text/plain',
    'xht'     => 'application/xhtml+xml',
    'xhtml'   => 'application/xhtml+xml',
    'xls'     => 'application/vnd.ms-excel',
    'xml'     => 'application/xml',
    'zip'     => 'application/zip'
  );

    public static function Display(string $rout){
        if (is_file(__ROOT__ . $rout)){
            $File = pathinfo(__ROOT__ . $rout);
            if (isset(self::$Types[$File["extension"]])){
                $seconds_to_cache = 84600 *7;
                header("Cache-Control: must-revalidate,max-age={$seconds_to_cache}");
                header("Content-Type: ".self::$Types[$File["extension"]]);
                header("Content-Length: " . filesize(__ROOT__ . $rout));
                header("Content-Disposition: attachment; filename={$File["basename"]}");
                header('Content-Transfer-Encoding: base64');
                header("Expires: " . gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT");
                header("Pragma: cache");
                http_response_code(200);
                readfile(__ROOT__ . $rout);
                exit;
            }
        }
    }


}