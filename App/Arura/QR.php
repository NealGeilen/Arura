<?php
namespace Arura;

use Arura\Settings\Application;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class QR{
    /**
     * @param string $sHref
     * @param null $sCacheFile
     * @return mixed
     */
    public static function Create($sContent= "", $WithImage = false)
    {
        $Builder = Builder::create();
        $Builder
            ->writer(new PngWriter())
            ->data($sContent)
            ->size(300)
            ->margin(10)
            ->validateResult(true)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->encoding(new Encoding('UTF-8'));
        ;

        $colorJson = Application::get("QR-Code", "Color");
        if (isJson($colorJson) && !empty($colorJson)){
            $colors = json_array_decode($colorJson);
            $Builder->foregroundColor(new Color($colors["r"], $colors["g"], $colors["b"]));
        }
        $sLogo = Application::get("QR-Code", "Logo");
        if (!empty($sLogo) && is_file(__WEB__ROOT__ . $sLogo) && $WithImage){
            $Builder->logoPath(__WEB__ROOT__ . $sLogo);
            $Builder->logoResizeToWidth(100);
        }


        return  $Builder->build()->getDataUri();
    }
}