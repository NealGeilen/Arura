<?php
namespace Arura;

use Arura\Settings\Application;
use Endroid\QrCode\QrCode;

class QR{
    /**
     * @param string $sHref
     * @param null $sCacheFile
     * @return mixed
     */
    public static function Create($sContent= "")
    {
        $qrCode = new QrCode($sContent);
        $qrCode->setSize(300);
        $colorJson = Application::get("QR-Code", "Color");
        if (isJson($colorJson) && !empty($colorJson)){
            $qrCode->setForegroundColor(json_array_decode($colorJson));
        }
        $sLogo = Application::get("QR-Code", "Logo");
        if (!empty($sLogo) && is_file(__WEB__ROOT__ . $sLogo)){
            $qrCode->setLogoPath(__WEB__ROOT__ . $sLogo);
            $qrCode->setLogoSize(100,100);
        }

        $qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_MARGIN); // The size of the qr code is shrinked, if necessary, but the size of the final image remains unchanged due to additional margin being added (default)
        $qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_ENLARGE); // The size of the qr code and the final image is enlarged, if necessary
        $qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK);

        return  $qrCode->writeDataUri();
    }
}