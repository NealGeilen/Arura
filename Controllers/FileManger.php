<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Database;
use Arura\Permissions\Right;
use Arura\Router;

class FileManger extends AbstractController {

    public function Home(){
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/FileManger/Home.js");
        $this->render("AdminLTE/Pages/FileManger/Home.tpl", [
            "title" =>"Bestanden"
        ]);
    }

}