<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Database;
use Arura\Pages\Page;
use Arura\Permissions\Right;
use Arura\Router;

class CMS extends AbstractController {

    public function Pages(){
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/CMS/Pages.js");
        $this->render("AdminLTE/Pages/CMS/Pages.tpl", [
            "title" =>"Pagina's"
        ]);
    }

    public function Menu(){
        Router::addSourceScriptJs(__ARURA__ROOT__ . "/assets/vendor/Nestable2-1.6.0/dist/jquery.nestable.min.js");
        Router::addSourceScriptCss(__ARURA__ROOT__ ."/assets/vendor/Nestable2-1.6.0/dist/jquery.nestable.min.css");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/CMS/Menu.js");
        $this->render("AdminLTE/Pages/CMS/Menu.tpl", [
            "title" =>"Menu"
        ]);
    }

    public function Content($id){
        $p = new \Arura\Pages\CMS\Page($id);
        Router::getSmarty() -> assign('aCmsPage', $p->__toArray());
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/CMS/Content.js");
        Router::addSourceScriptCss(__ARURA_TEMPLATES__ . "AdminLTE/Pages/CMS/Content.css");
        $this->render("AdminLTE/Pages/CMS/Content.tpl", [
            "title" =>"Pagina content",
            "sPageSideBar" => Router::getSmarty()->fetch(__ARURA_TEMPLATES__ . "AdminLTE/Pages/CMS/Content.Sidebar.tpl")
        ]);
    }

    public function Settings($id){
        $p = new \Arura\Pages\CMS\Page($id);
        Router::getSmarty() -> assign('aCmsPage', $p->__toArray());
        $this->render("AdminLTE/Pages/CMS/Settings.tpl", [
            "title" =>"Pagina instellingen"
        ]);
    }

}