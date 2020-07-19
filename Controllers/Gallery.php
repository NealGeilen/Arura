<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Router;

class Gallery extends AbstractController {

    public function Home(){
        $smarty = Router::getSmarty();
        $smarty->assign("aGalleries", \Arura\Gallery\Gallery::getAllGalleries(false));
//        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/FileManger/Home.js");
        $this->render("AdminLTE/Pages/Gallery/Home.tpl", [
            "title" =>"Album's"
        ]);
    }

    public function Gallery($id){
        $gallery = new \Arura\Gallery\Gallery($id);
        $this->render("AdminLTE/Pages/Gallery/Gallery.tpl", [
            "title" =>$gallery->getName(),
            "Gallery" => $gallery
        ]);
    }

}