<?php
namespace Arura\CMS\Page;

use NG\Database;

class Plugin{

    protected $oDatabase;


    public function __construct(){
        $this -> oDatabase = new Database();
    }


}