<?php
namespace Arura;

use chillerlan\QRCode\QRCode;

class QR{
    /**
     * @param string $sHref
     * @param null $sCacheFile
     * @return mixed
     */
    public static function Create($sHref = "", $sCacheFile = null){
        return (new QRCode)->render($sHref, $sCacheFile);
    }
}