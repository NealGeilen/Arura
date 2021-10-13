<?php
namespace Arura\Updater;

use Arura\Exceptions\Error;
use Arura\Pages\CMS\Addon;
use Arura\Pages\Page;
use Arura\Router;
use Arura\Settings\Application;
use mikehaertl\shellcommand\Command;

class Updater{

    const COMPOSER = "php composer.phar";
    protected static $MAIN_DIR = null;

    public function __construct()
    {
        if (DEV_MODE) {
            self::$MAIN_DIR = __ARURA__ROOT__;
        } else {
            self::$MAIN_DIR = __ROOT__;
        }
    }

    /**
     * @return array|int|mixed|null
     * @throws Error
     */
    public function getPackagesNeededUpdate(){
        $command = new Command(self::COMPOSER." show -o --format=json --direct");
        $command->procCwd = self::$MAIN_DIR;
        $command->procEnv = getenv();
        if ($command->execute()) {
            return json_array_decode($command->getOutput());
        } else {
            if ((bool)Application::get("arura", "Debug")){
                return $command->getError(false);
            }
            return  $command->getExitCode();
        }
    }

    public function updatePackage($name = ""){
        $command = new Command(self::COMPOSER." update {$name}");
        $command->procCwd = self::$MAIN_DIR;
        $command->procEnv = getenv();
        if ($command->execute()) {
            $this->createNewAssets();
            return (string)$command->getOutput();
        } else {
            return $exitCode = $command->getExitCode();
        }
    }

    public function updateAllPackages(){
        $command = new Command(self::COMPOSER." update");
        $command->procCwd = self::$MAIN_DIR;
        $command->procEnv = getenv();
        if ($command->execute()) {
            $this->createNewAssets();
            return (string)$command->getOutput();
        } else {
            return $exitCode = $command->getExitCode();
        }
    }

    public function removePackage(string $packageName){
        $command = new Command(self::COMPOSER." remove " . $packageName);
        $command->procCwd = self::$MAIN_DIR;
        $command->procEnv = getenv();
        if ($command->execute()) {
            $this->createNewAssets();
            return (string)$command->getOutput();
        } else {
            return $exitCode = $command->getExitCode();
        }
    }


    protected function createNewAssets(){
        deleteItem(__WEB__ROOT__ . DIRECTORY_SEPARATOR . "cached");
        if (mkdir(__WEB__ROOT__ . DIRECTORY_SEPARATOR . "cached")){
            Addon::CacheAllAddons();
            Page::getCacher()->getMinifyedFiles();
            (new Router(null))->readDir();
            return true;
        }
        return false;
    }
}