<?php
namespace Arura;

use Arura\Settings\Application;
use Arura\User\User;

abstract class AbstractController{

    protected final function isXmlHttpRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function throwNotFound(){
        header('HTTP/1.1 404 Not Found');
        throw new \Exception("Page not found", 404);
    }

    public function throwAccessDenied(){
        header('HTTP/1.1 403 Access Denied');
        throw new \Exception("Access Denied", 403);
    }

    /**
     * @param string|array $i
     * @param array $parameters
     */
    protected final function render($i, $parameters = []){
        if(is_array($i)){
            echo json_encode($i);
        } else  if (is_file(__ARURA_TEMPLATES__ . $i)){
            foreach ($parameters as $key => $value){
                Router::getSmarty()->assign($key, $value);
            }
            if (!isset($parameters["sPageSideBar"])){
                Router::getSmarty()->assign("sPageSideBar", null);
            }
            Router::getSmarty()->assign("sRequestUrl", $_GET["_url_"]);
            Router::getSmarty()->assign("aManifest", json_array_decode(file_get_contents(__ARURA__ROOT__ . DIRECTORY_SEPARATOR . "_app" . DIRECTORY_SEPARATOR . "manifest.json")));

            Router::getSmarty()->assign("aUser", (User::isLogged()) ? User::activeUser()->__toArray() : null);
            Router::getSmarty()->assign("aArura" ,["dir" => __ARURA__DIR_NAME__, "api" => "api"]);
            Router::getSmarty()->assign("bMobileUser", isUserOnMobile());
            Router::getSmarty()->assign("aWebsite" ,Application::getAll()["website"]);
            Router::getSmarty()->assign('aResourceFiles', ["page" => Router::loadResourceOfPageFiles($parameters["title"]), "arura" => Router::loadResourceFiles()]);
            Router::getSmarty()->display(__ARURA_TEMPLATES__. $i);
            exit;
        }
    }


}