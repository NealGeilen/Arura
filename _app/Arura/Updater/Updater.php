<?php
namespace Arura\Updater;

use mikehaertl\shellcommand\Command;

class Updater{

    const COMPOSER = "php composer.phar";
    protected static $MAIN_DIR = null;

    public function __construct()
    {
        if (DEV_MODE){
            self::$MAIN_DIR = __ARURA__ROOT__;
        } else {
            self::$MAIN_DIR = __ROOT__;
        }

    }

    public function getCurrentAruraVersion(){
        if (DEV_MODE){
            return  "DEV MODE";
        } else {
            return json_array_decode(file_get_contents(__VENDOR__ . "../" . "composer.json"))["require"]["arura/dashboard"];
        }
    }

    /**
     * @return int|string|null
     */
    public function getPackagesNeededUpdate(){
        $command = new Command(self::COMPOSER." show -o --format=json --direct");
        $command->procCwd = self::$MAIN_DIR;
        if ($command->execute()) {
            return json_array_decode($command->getOutput());
        } else {
            return $exitCode = $command->getExitCode();
        }
    }

    public function updatePackage($name = ""){
        $command = new Command(self::COMPOSER." update {$name}");
        $command->procCwd = self::$MAIN_DIR;
        if ($command->execute()) {
            return (string)$command->getOutput();
        } else {
            return $exitCode = $command->getExitCode();
        }
    }

    public function updateAllPackages(){
        $command = new Command(self::COMPOSER." update");
        $command->procCwd = self::$MAIN_DIR;
        if ($command->execute()) {
            return (string)$command->getOutput();
        } else {
            return $exitCode = $command->getExitCode();
        }
    }
}