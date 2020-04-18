<?php
namespace App\Controllers;
use Arura\AbstractController;
use Arura\Database;
use Arura\Permissions\Right;
use Arura\Router;

class Analytics extends AbstractController {

    public function Home(){
        Router::addSourceScriptJs("/dashboard/assets/vendor/d3/d3.min.js");
        Router::addSourceScriptJs("/dashboard/assets/vendor/topojson/topojson.min.js");
        Router::addSourceScriptJs("/dashboard/assets/vendor/datamaps/datamaps.world.min.js");
        Router::addSourceScriptJs(__ARURA_TEMPLATES__ . "AdminLTE/Pages/Analytics/Home.js");
        $this->render("AdminLTE/Pages/Analytics/Home.tpl", [
            "title" =>"Analytics"
        ]);
    }

}