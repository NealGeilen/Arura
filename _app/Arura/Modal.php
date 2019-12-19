<?php
namespace Arura;

Abstract class Modal{
    protected $isLoaded = false;
    protected $db;

    public function __construct()
    {
        $this->db = new \NG\Database();
    }



}