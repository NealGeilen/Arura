<?php
namespace Arura;

class Cache{

    public static $Types = array(
        "woff" => "font/woff",
        "woff2" => "font/woff2",
        "ttf" => "font/ttf",
        'otf'   => "font/otf",
        'css'   => 'text/css',
        'svg'   => "image/svg+xml",
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
    'zip'     => 'application/zip',

  );

    public static function Display(string $rout,string $title = null,bool $download = false){
        if (is_file($rout)){
            $File = pathinfo($rout);
            if (isset(self::$Types[$File["extension"]])){
                $day = 84600;

                $a = explode("/", self::$Types[$File["extension"]],2);

                switch ($a[0]){
                    case "image":
                    case "font":
                        $seconds_to_cache = $day * 365;
                        break;
                    case "text":
                        $seconds_to_cache = $day * 7;
                        break;
                    case "application":
                        $seconds_to_cache = $day * 14;
                        break;
                    default:
                        $seconds_to_cache = $day *5;
                        break;
                }

                if (is_null($title)){
                    $title = $File["basename"];
                }

                if ($download){
                    header("Content-Disposition: attachment; filename={$title}");
                    header("Content-Type: application/force-download");
                } else {
                    header("Content-Disposition: inline; filename={$title}");
                }
                header("Cache-Control: must-revalidate,max-age={$seconds_to_cache}");
                header("Content-Type: ".self::$Types[$File["extension"]]);
                header("Content-Length: " . filesize($rout));
                header('Content-Transfer-Encoding: base64');
                header("Expires: " . gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT");
                header("Pragma: cache");

                http_response_code(200);
                readfile($rout);
                exit;
            }
        }
    }


}