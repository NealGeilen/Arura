<?php
namespace App\Controllers;

use Arura\AbstractController;
use Arura\Settings\Application;
use Exception;

class Errors extends AbstractController {


    public function error(Exception $exception){
        switch ($exception->getCode()){
            case 403:
                $this->redirect("/dashboard/login");
                break;
        }
        http_response_code($exception->getCode());
        $this->render("Errors/index.tpl",[
            "title" => "error",
            "exception" => $exception,
            "debug" => Application::get("arura", "Debug")
        ]);
    }
}