<?php
namespace Arura;

use chillerlan\QRCode\QRCode;

class QR{
    public static function Create($sHref, $sCacheFile = null){
        return (new QRCode)->render($sHref, $sCacheFile);
    }
}