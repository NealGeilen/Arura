<?php
namespace Arura;

use Arura\Exceptions\Forbidden;
use Arura\Exceptions\NotFound;
use Arura\Settings\Application;
use Arura\User\User;

abstract class AbstractController{

    protected final function isXmlHttpRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function throwNotFound(){
        header('HTTP/1.1 404 Not Found');
        throw new NotFound("Page not found");
    }

    public function throwAccessDenied(){
        header('HTTP/1.1 403 Access Denied');
        throw new Forbidden();
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
            $sVersion = json_array_decode(file_get_contents(__ARURA__ROOT__ . "composer.json"))["version"];
            if (!isset($_GET["_URL"])){
                $_GET["_URL"] = null;
            }

            Router::getSmarty()->assign("Flashes", json_encode(Flasher::getFlashes()));
            Router::getSmarty()->assign("aUser", (User::isLogged()) ? User::activeUser()->__toArray() : null);
            Router::getSmarty()->assign("aArura" ,["dir" => __ARURA__DIR_NAME__, "api" => "api", "version" => $sVersion]);
            Router::getSmarty()->assign("bMobileUser", isUserOnMobile());
            Router::getSmarty()->assign("aWebsite" ,Application::getAll()["website"]);
            Router::getSmarty()->assign('aResourceFiles', ["page" => Router::getCachedFile($_GET["_URL"]), "arura" => Router::getCachedFile("MAIN", false)]);
            Router::getSmarty()->display(__ARURA_TEMPLATES__. $i);
            exit;
        }
    }

    protected final function addParameter($name, $value){
        Router::getSmarty()->assign($name, $value);
    }

    protected final function redirect($url = ""){
        redirect($url);
    }


}